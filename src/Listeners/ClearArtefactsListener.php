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
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Adapter\PhpFilesystemAdapterInterface;

/**
 * An listener implementation that clears the artefact files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ClearArtefactsListener extends AbstractListener
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
     * @param \League\Event\EventInterface $event  The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // clear the filecache
        clearstatcache();

        // load the serial and directories to clear
        $files = $this->getLoader()->load();

        // iterate over the artefacts and clear
        // them as well as the flag files
        foreach ($files as $file) {
            // prepare the glob pattern to load the artefact as well as it's flag files
            $pattern = sprintf('%s/%s.*', dirname($file), pathinfo($file, PATHINFO_FILENAME));
            // iterate over the found files and delete them
            foreach (glob($pattern) as $filename) {
                $this->getFilesystemAdapter()->delete($filename);
            }
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
}
