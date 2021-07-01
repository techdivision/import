<?php

/**
 * TechDivision\Import\Listeners\ArchiveListener
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
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Listener that archives the import artefacts.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ArchiveListener extends AbstractListener
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
     * Handle the event.
     *
     * @param \League\Event\EventInterface              $event       The event that triggered the listener
     * @param \TechDivision\Import\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {

        // query whether or not, the import artefacts have to be archived
        if (!$this->getConfiguration()->haveArchiveArtefacts()) {
            $this->getSystemLogger()->info('Archiving functionality has not been activated');
            return;
        }

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // load the number of imported bunches from the status
        $bunches = $status[RegistryKeys::BUNCHES];

        // if no files have been imported, return immediately
        if ($bunches === 0) {
            $this->getSystemLogger()->info('Found no files to archive');
            return;
        }

        // clear the filecache
        clearstatcache();

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // init file iterator on source directory
        $fileIterator = new \FilesystemIterator($sourceDir);

        // log the number of files that has to be archived
        $this->getSystemLogger()->info(sprintf('Found %d files to archive in directory %s', $bunches, $sourceDir));

        // try to load the archive directory
        $archiveDir = $this->getConfiguration()->getArchiveDir();

        // query whether or not the specified archive directory already exists
        if ($archiveDir === null) {
            // try to initialize a default archive directory by concatenating 'archive' to the target directory
            $archiveDir = sprintf('var/import_history');
        }

        // query whether or not the archive directory already exists
        if (!is_dir($archiveDir)) {
            // create the archive directory if possible
            if (mkdir($archiveDir, 0755, true)) {
                $this->getSystemLogger()->info(sprintf('Successfully created archived archive directory %s', $archiveDir));
            } else {
                // initialize the message that the archive directory can not be created
                $message = sprintf('Can\'t create archive directory %s', $archiveDir);

                // log a message if we're in debug mode, else throw an exception
                if ($this->getConfiguration()->isDebugMode()) {
                    $this->getSystemLogger()->error($message);
                } elseif ($this->getConfiguration()->isStrictMode()) {
                    throw new \Exception($message);
                }
            }
        }

        // create the ZIP archive
        $archive = new \ZipArchive();
        $archive->open($archiveFile = sprintf('%s/%s.zip', $archiveDir, $application->getSerial()), \ZipArchive::CREATE);

        // iterate through all files and add them to the ZIP archive
        /** @var \SplFileInfo $filename */
        foreach ($fileIterator as $filename) {
            if ($filename->isFile()) {
                $archive->addFile($filename, basename($filename));
            }
        }

        // save the ZIP archive
        $archive->close();

        // append the name of the archive file in the registry
        $this->getRegistryProcessor()->mergeAttributesRecursive(RegistryKeys::STATUS, array(RegistryKeys::ARCHIVE_FILE => $archiveFile));

        // and and log a message that the import artefacts have been archived
        $this->getSystemLogger()->info(sprintf('Successfully archived imported files to %s!', basename($archiveFile)));
    }
}
