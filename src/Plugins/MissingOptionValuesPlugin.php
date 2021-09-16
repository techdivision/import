<?php

/**
 * TechDivision\Import\Plugins\MissingOptionValuesPlugin
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Adapter\ExportAdapterInterface;

/**
 * Plugin that exports the missing option values to a CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MissingOptionValuesPlugin extends AbstractPlugin
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'missing-option-values';

    /**
     * The trait providing the export functionality.
     *
     * @var \TechDivision\Import\Plugins\ExportableTrait
     */
    use ExportableTrait;

    /**
     * The array containing the data for product type configuration (configurables, bundles, etc).
     *
     * @var array
     */
    protected $artefacts = array();

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\ApplicationInterface           $application   The application instance
     * @param \TechDivision\Import\Adapter\ExportAdapterInterface $exportAdapter The export adapter instance
     */
    public function __construct(ApplicationInterface $application, ExportAdapterInterface $exportAdapter)
    {

        // pass the application to the parent constructor
        parent::__construct($application);

        // set the export adapter
        $this->exportAdapter = $exportAdapter;
    }

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    public function isDebugMode()
    {
        return $this->getConfiguration()->isDebugMode();
    }

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        // query whether or not, the debug mode has been enabled
        if (!$this->getConfiguration()->isDebugMode()) {
            $this->getSystemLogger()->info('Debug mode is not enabled, missing option values will not be exported');
            return;
        }

        // clear the filecache
        clearstatcache();

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // load the array with the missing option values from the status
        $missingOptions = $status[RegistryKeys::MISSING_OPTION_VALUES];

        // if missing option values are found, return immediately
        if (sizeof($missingOptions) === 0) {
            $this->getSystemLogger()->info('Found no missing option values');
            return;
        }

        // initialize the array with the artefacts
        $artefacts = array();

        // prepare the artefacts
        foreach ($missingOptions as $attributeCode => $options) {
            foreach ($options as $value => $data) {
                list($counter, $skus) = $data;
                $artefacts[] = $this->newArtefact(
                    array(
                        ColumnKeys::STORE_VIEW_CODE   => null,
                        ColumnKeys::ATTRIBUTE_CODE    => $attributeCode,
                        ColumnKeys::ADMIN_STORE_VALUE => $value,
                        ColumnKeys::VALUE             => $value,
                        ColumnKeys::COUNTER           => $counter,
                        ColumnKeys::UNIQUE_IDENTIFIER => implode(',', array_keys($skus)),
                        ColumnKeys::SORT_ORDER        => null
                    )
                );
            }
        }

        // initialize a dummy last entity ID
        $this->setLastEntityId(0);

        // add the artefacts (missing option values) and export them as CSV file
        $this->addArtefacts(MissingOptionValuesPlugin::ARTEFACT_TYPE, $artefacts);
        $this->export(date('Ymd-His'), $counter = '01');

        // query whether or not a swift mailer has been registered
        if ($swiftMailer = $this->getSwiftMailer()) {
            // the swift mailer configuration
            $swiftMailerConfiguration = $this->getPluginConfiguration()->getSwiftMailer();

            // create the message with the CSV with the missing option values
            $message = $swiftMailer->createMessage()
                                   ->setSubject(sprintf('[%s] %s', $this->getSystemName(), $swiftMailerConfiguration->getParam(SwiftMailerKeys::SUBJECT)))
                                   ->setFrom($swiftMailerConfiguration->getParam(SwiftMailerKeys::FROM))
                                   ->setTo($to = $swiftMailerConfiguration->getParam(SwiftMailerKeys::TO))
                                   ->setBody('The attached CSV file(s) contains the missing attribute option values');

            // load the exported filenames
            $exportedFilenames = $this->getExportAdapter()->getExportedFilenames();

            // attach the CSV files with the missing option values
            foreach ($exportedFilenames as $filename) {
                $message->attach(\Swift_Attachment::fromPath($filename));
            }

            // initialize the array with the failed recipients
            $failedRecipients = array();
            $recipientsAccepted = 0;

            // send the mail
            $recipientsAccepted = $swiftMailer->send($message, $failedRecipients);

            // query whether or not all recipients have been accepted
            if (sizeof($failedRecipients) > 0) {
                $this->getSystemLogger()->error(sprintf('Can\'t send mail to %s', implode(', ', $failedRecipients)));
            }

            // if at least one recipient has been accepted
            if ($recipientsAccepted > 0) {
                // cast 'to' into an array if not already
                is_array($to) ? : $to = (array) $to;
                // remove the NOT accepted recipients
                $acceptedRecipients = array_diff($to, $failedRecipients);

                // log a message with the successfull receivers
                $this->getSystemLogger()->info(
                    sprintf(
                        'Mail successfully sent to %d recipient(s) (%s)',
                        $recipientsAccepted,
                        implode(', ', $acceptedRecipients)
                    )
                );
            }
        }

        // load the system loggers
        $systemLoggers = $this->getSystemLoggers();

        // and and log a message that the missing option values has been exported
        foreach ($systemLoggers as $systemLogger) {
            $systemLogger->error(
                sprintf(
                    'Exported missing option values to file %s!',
                    $filename
                )
            );
        }
    }

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts()
    {
        return $this->artefacts;
    }

    /**
     * Reset the array with the artefacts to free the memory.
     *
     * @return void
     */
    public function resetArtefacts()
    {
        $this->artefacts = array();
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param string  $type      The artefact type, e. g. configurable
     * @param array   $artefacts The product type artefacts
     * @param boolean $override  Whether or not the artefacts for the actual entity ID has to be overwritten
     *
     * @return void
     * @uses \TechDivision\Import\Product\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts($type, array $artefacts, $override = true)
    {

        // query whether or not, any artefacts are available
        if (sizeof($artefacts) === 0) {
            return;
        }

        // serialize the original data, if we're in debug mode
        $keys = array_keys($artefacts);
        foreach ($keys as $key) {
            if (isset($artefacts[$key][ColumnKeys::ORIGINAL_DATA])) {
                $artefacts[$key][ColumnKeys::ORIGINAL_DATA] = $this->isDebugMode() ? serialize($artefacts[$key][ColumnKeys::ORIGINAL_DATA]) : null;
            }
        }

        // query whether or not, existing artefacts has to be overwritten
        if ($override === true) {
            $this->overrideArtefacts($type, $artefacts);
        } else {
            $this->appendArtefacts($type, $artefacts);
        }
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID and overrides existing ones with the same key.
     *
     * @param string $type      The artefact type, e. g. configurable
     * @param array  $artefacts The product type artefacts
     *
     * @return void
     */
    protected function overrideArtefacts($type, array $artefacts)
    {
        foreach ($artefacts as $key => $artefact) {
            $this->artefacts[$type][$this->getLastEntityId()][$key] = $artefact;
        }
    }

    /**
     * Append's the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param string $type      The artefact type, e. g. configurable
     * @param array  $artefacts The product type artefacts
     *
     * @return void
     */
    protected function appendArtefacts($type, array $artefacts)
    {
        foreach ($artefacts as $artefact) {
            $this->artefacts[$type][$this->getLastEntityId()][] = $artefact;
        }
    }

    /**
     * Return's the systemm name to be used.
     *
     * @return string The system name to be used
     */
    protected function getSystemName()
    {
        return $this->getConfiguration()->getSystemName();
    }

    /**
     * Return's the target directory the CSV files has to be exported to.
     *
     * @return string The name of the target directory
     */
    public function getTargetDir()
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // return the source directory where we want to export to
        return $sourceDir;
    }
}
