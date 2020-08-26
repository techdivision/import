<?php

/**
 * TechDivision\Import\Listeners\ImportHistoryListener
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Listener that adds a record to the import Magento import history.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImportHistoryListener extends AbstractListener
{

    /**
     * The trait that provides basic system logger functionality.
     *
     * @var \TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\ImportProcessorInterface
     */
    protected $importProcessor;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Services\ImportProcessorInterface    $importProcessor   The import processor instance
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers     The array with the system loggers instances
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The configuration instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        ImportProcessorInterface $importProcessor,
        Collection $systemLoggers,
        ConfigurationInterface $configuration
    ) {

        // set the passed instances
        $this->registryProcessor = $registryProcessor;
        $this->importProcessor = $importProcessor;
        $this->systemLoggers = $systemLoggers;
        $this->configuration = $configuration;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    protected function getImportProcessor()
    {
        return $this->importProcessor;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Initialize's and return's a new entity with the status 'create'.
     *
     * @param array $attr The attributes to merge into the new entity
     *
     * @return array The initialized entity
     */
    protected function initializeEntity(array $attr = array())
    {
        return array_merge(array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE), $attr);
    }

    /**
     * Load's and return's the admin user with the passed username.
     *
     * @param string $username The username of the admin user to return
     *
     * @return array|null The admin user with the passed username
     */
    protected function loadAdminUserByUsername($username)
    {

        // query whether or not at least one admin user is available
        if (sizeof($adminUsers = $this->getImportProcessor()->getAdminUsers()) === 0) {
            throw new \Exception('Can\'t find any admin user to update import history with');
        }

        // try to find the admin user with the passed passeword
        foreach ($adminUsers as $adminUser) {
            if ($adminUser[MemberNames::USERNAME] === $username) {
                return $adminUser;
            }
        }

        // log a warning that the admin user with the passed username is not available
        $this->getSystemLogger()->debug(sprintf('Admin user with name "%s" is not available, use first available user to save import history', $username));

        // return the first admin user otherwise
        return current($adminUsers);
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface              $event       The event that triggered the listener
     * @param \TechDivision\Import\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // query whether or not a status of the actual import is available
        if ($registryProcessor->hasAttribute(RegistryKeys::STATUS)) {
            // load the status from the registry
            $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

            // initialize the date with the start time
            $startedAt = new \DateTime();
            $startedAt->setTimestamp($status[RegistryKeys::STARTED_AT]);

            // initialize the date with the finish time
            $finishedAt = new \DateTime();
            $finishedAt->setTimestamp($status[RegistryKeys::FINISHED_AT]);

            // calculate the execution time
            $interval = $finishedAt->diff($startedAt);
            $executionTime = $interval->format('%H:%I:%S');

            // initialize the counter for the processed rows and
            // the arrays for the error files and messages
            $processedRows = 0;
            $errorMessages = array();

            // query whether or not we've processed files in the registry
            if (isset($status[RegistryKeys::FILES])) {
                // load the processed files
                $files = $status[RegistryKeys::FILES];
                // count the complete number of processed rows
                foreach ($files as $metadatas) {
                    foreach ($metadatas as $metadata) {
                        // we've an error, if the status is NOT 1
                        if ($metadata[RegistryKeys::STATUS] === 2) {
                            // append the message of the errored file
                            $errorMessages[] = $metadata[RegistryKeys::ERROR_MESSAGE];
                        }
                        // count the number of processed rows
                        $processedRows += (integer) $metadata[RegistryKeys::PROCESSED_ROWS];
                    }
                }
            }

            // load the name of the executed command from the configuration
            $commandName = $this->getConfiguration()->getCommandName();

            // prepare the summary for the history in Magento backend
            $summary = sprintf(
                'Imported %d rows when executing command %s with ❤ by %s %s',
                $processedRows,
                $commandName,
                $application->getName(),
                $application->getVersion()
            );

            // try to load the archive from the registry
            $archiveFile = 'n/a';
            if ($this->getConfiguration()->haveArchiveArtefacts()) {
                if (isset($status[RegistryKeys::ARCHIVE_FILE])) {
                    $archiveFile = $status[RegistryKeys::ARCHIVE_FILE];
                }
            } else {
                $archiveFile = 'Archiving has been Deactivated';
            }

            // load the admin user to persist the import history with
            $adminUser = $this->loadAdminUserByUsername($this->getConfiguration()->getUsername());

            // initialize the entity with the import history data
            $importHistory = $this->initializeEntity(
                array(
                    MemberNames::USER_ID        => $adminUser[MemberNames::USER_ID],
                    MemberNames::STARTED_AT     => $startedAt->format('Y-m-d H:i:s'),
                    MemberNames::IMPORTED_FILE  => $archiveFile,
                    MemberNames::EXECUTION_TIME => $executionTime,
                    MemberNames::SUMMARY        => substr(sizeof($errorMessages) > 0 ? implode(', ', $errorMessages) : $summary, 0, 255),
                    MemberNames::ERROR_FILE     => sizeof($errorMessages) > 0 ? $archiveFile : 'n/a'
                )
            );

            // persist the import history
            $this->getImportProcessor()->persistImportHistory($importHistory);

            // log a message that the import history has been updated successfully
            $this->getSystemLogger()->debug(
                sprintf('Successfully add new entry to import history for import "%s"', $application->getSerial())
            );
        }
    }
}
