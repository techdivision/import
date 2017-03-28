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
use TechDivision\Import\Utils\ExporterTrait;
use TechDivision\Import\Utils\SwiftMailerKeys;

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
     * The exporter trait implementation.
     *
     * @var \TechDivision\Import\Utils\ExporterTrait
     */
    use ExporterTrait;

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

        // intialize array for the missing option values with the CSV headers
        $toBeCreated = array(
            array(
                ColumnKeys::STORE_VIEW_CODE,
                ColumnKeys::ATTRIBUTE_CODE,
                ColumnKeys::VALUE,
                ColumnKeys::COUNTER,
                ColumnKeys::SORT_ORDER
            )
        );

        // append the missing option values to the array
        foreach ($missingOptions as $attributeCode => $options) {
            foreach ($options as $value => $counter) {
                $toBeCreated[] = array(null, $attributeCode, $value, $counter, null);
            }
        }

        // prepare the filename and export the missing options as CSV file
        $filename = sprintf('%s/missing-option-values.csv', $sourceDir);
        $this->getExporterInstance()->export($filename, $toBeCreated);

        // query whether or not a swift mailer has been registered
        if ($swiftMailer = $this->getSwiftMailer()) {
            // the swift mailer configuration
            $swiftMailerConfiguration = $this->getPluginConfiguration()->getSwiftMailer();

            // create the message with the CSV with the missing option values
            $message = $swiftMailer->createMessage()
                                   ->setSubject($swiftMailerConfiguration->getParam(SwiftMailerKeys::SUBJECT))
                                   ->setFrom($swiftMailerConfiguration->getParam(SwiftMailerKeys::FROM))
                                   ->setTo($to = $swiftMailerConfiguration->getParam(SwiftMailerKeys::TO))
                                   ->setBody('The attached CSV file contains the missing attribute option values')
                                   ->attach(\Swift_Attachment::fromPath($filename));

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
}
