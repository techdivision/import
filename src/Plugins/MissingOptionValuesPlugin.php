<?php

/**
 * TechDivision\Import\Plugins\MissingOptionValuesPlugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Subjects\ExportableTrait;
use TechDivision\Import\Adapter\ExportAdapterInterface;

/**
 * Plugin that exports the missing option values to a CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    use ExportableTrait;

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
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

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
        $this->addArtefacts($artefactType = MissingOptionValuesPlugin::ARTEFACT_TYPE, $artefacts);
        $this->export($timestamp = date('Ymd-His'), $counter = '01');

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

            // attach the CSV files with the missing option values
            foreach ($this->getExportAdapter()->getExportedFilenames() as $filename) {
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

        // and and log a message that the missing option values has been exported
        foreach ($this->getSystemLoggers() as $systemLogger) {
            $systemLogger->error(
                sprintf(
                    'Exported missing option values to file %s!',
                    $filename
                )
            );
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
    protected function getTargetDir()
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // return the source directory where we want to export to
        return $sourceDir;
    }
}
