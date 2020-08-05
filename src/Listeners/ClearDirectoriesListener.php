<?php

/**
 * TechDivision\Import\Listeners\ClearDirectoriesListener
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
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Adapter\PhpFilesystemAdapterInterface;

/**
 * An listener implementation that clears source and target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ClearDirectoriesListener extends AbstractListener
{

    /**
     * The iterator with the directory names that has to be cleared.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The filesystem adapter instance to use.
     *
     * @var \TechDivision\Import\Adapter\PhpFilesystemAdapterInterface
     */
    protected $filesystemAdapter;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Adapter\PhpFilesystemAdapterInterface $filesystemAdapter The filesystem adapter used to clear the directories
     * @param \TechDivision\Import\Loaders\LoaderInterface               $loader            The directory loader instance
     */
    public function __construct(
        PhpFilesystemAdapterInterface $filesystemAdapter,
        LoaderInterface $loader
    ) {
        $this->filesystemAdapter = $filesystemAdapter;
        $this->loader = $loader;
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

        // clear the filecache
        clearstatcache();

        // load the serial and directories to clear
        $serial = $application->getSerial();
        $directories = $this->getLoader()->load();

        //  clear the loaded directories
        foreach ($directories as $directory) {
            $this->clearDirectory($directory, $serial);
        }
    }

    /**
     * Return's the fileadapter instance to use.
     *
     * @return \TechDivision\Import\Adapter\PhpFilesystemAdapterInterface The filesystem adapter to use
     */
    protected function getFilesystemAdapter()
    {
        return $this->filesystemAdapter;
    }

    /**
     * The loader that loads the directory names that has to be cleared.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * Delete's all files from the passed directory and deletes the directory
     * as well if it contains no more files.
     *
     * @param string $directory The directory that has to be cleared
     * @param string $serial    The serial of the actual import process
     *
     * @return void
     */
    protected function clearDirectory($directory, $serial)
    {

        // load the filesystem adapter and init
        // file iterator on source directory
        $filesystemAdapter = $this->getFilesystemAdapter();

        // query whether or not we've a directory and it IS empty
        if ($filesystemAdapter->isDir($directory) && sizeof($filesystemAdapter->listContents($directory, true)) === 0) {
            // remove the directory ONLY it has been created during the import process
            if (preg_match(sprintf('/.*\/%s$/', $serial), $directory) === 1) {
                $filesystemAdapter->removeDir($directory);
            }
        }
    }
}
