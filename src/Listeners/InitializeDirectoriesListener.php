<?php

/**
 * TechDivision\Import\Listeners\InitializeDirectoriesListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;

/**
 * An listener implementation that initialize source and target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class InitializeDirectoriesListener extends AbstractListener
{

    /**
     * The filesystem adapter instance to use.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    protected $filesystemAdapter;

    /**
     * The iterator with the directory names that has to be cleared.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter used to clear the directories
     * @param \TechDivision\Import\Loaders\LoaderInterface            $loader            The directory loader instance
     */
    public function __construct(FilesystemAdapterInterface $filesystemAdapter, LoaderInterface $loader)
    {
        $this->filesystemAdapter = $filesystemAdapter;
        $this->loader = $loader;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // clear the filecache
        clearstatcache();

        // load the directories to clear
        $directories = $this->getLoader()->load();

        //  clear the loaded directories
        foreach ($directories as $directory) {
            $this->initializeDirectory($directory);
        }
    }

    /**
     * Return's the fileadapter instance to use.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter to use
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
     * Create's the passed directory.
     *
     * @param string $directory The directory that has to be created
     *
     * @return void
     */
    protected function initializeDirectory($directory)
    {
        $this->getFilesystemAdapter()->mkdir($directory);
    }
}
