<?php

/**
 * TechDivision\Import\Plugins\ArchivePlugin
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

use TechDivision\Import\Utils\RegistryKeys;

/**
 * Plugin that archives the artefacts.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ArchivePlugin extends AbstractPlugin
{

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
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
            $archiveDir = sprintf('var/archive');
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
                } else {
                    throw new \Exception($message);
                }
            }
        }

        // create the ZIP archive
        $archive = new \ZipArchive();
        $archive->open($archiveFile = sprintf('%s/%s.zip', $archiveDir, $this->getSerial()), \ZipArchive::CREATE);

        // iterate through all files and add them to the ZIP archive
        /** @var \SplFileInfo $filename */
        foreach ($fileIterator as $filename) {
            if ($filename->isFile()) {
                $archive->addFile($filename, basename($filename));
            }
        }

        // save the ZIP archive
        $archive->close();

        // and and log a message that the import artefacts have been archived
        $this->getSystemLogger()->info(sprintf('Successfully archived imported files to %s!', $archiveFile));
    }
}
