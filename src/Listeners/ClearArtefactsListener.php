<?php

/**
 * TechDivision\Import\Listeners\ClearDirectoriesListener
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
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use Doctrine\Common\Collections\Collection;

/**
 * An listener implementation that clears the artefact files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ClearArtefactsListener extends AbstractListener
{

    /**
     * The trait that provides basic system logger functionality.
     *
     * @var \TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The iterator with the directory names that has to be cleared.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The filesystem adapter instance to use.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    protected $filesystemAdapter;

    /**
     * The actual configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The actual configuration instance
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface   $filesystemAdapter The filesystem adapter used to clear the directories
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers     The array with the system loggers instances
     * @param \TechDivision\Import\Loaders\LoaderInterface              $loader            The directory loader instance
     */
    public function __construct(
        ConfigurationInterface $configuration,
        FilesystemAdapterInterface $filesystemAdapter,
        Collection $systemLoggers,
        LoaderInterface $loader
    ) {
        $this->configuration = $configuration;
        $this->filesystemAdapter = $filesystemAdapter;
        $this->systemLoggers = $systemLoggers;
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

        // query whether or not we've to clear the artefacts
        if (!$this->getConfiguration()->haveClearArtefacts()) {
            $this->getSystemLogger()->info('Clear artefacts functionality has not been activated');
            return;
        }

        // clear the filecache
        clearstatcache();

        // load the serial and directories to clear
        $files = $this->getLoader()->load();

        // initialize the array for the patterns
        $patterns = array();

        // iterate over the artefacts and clear
        // them as well as the flag files
        foreach ($files as $file) {
            // exctract the components of the artefact file
            $elements = explode('_', pathinfo($file, PATHINFO_FILENAME));
            // build filenames for the possible element combinations,
            // which is necesary because of the .OK file possiblities
            for ($i = sizeof($elements); $i > 0; $i--) {
                // prepare the filename to build the pattern with
                $filename = implode('_', array_slice($elements, 0, $i));
                // prepare the glob pattern to load the artefact as well as it's flag files
                $patterns[$filename] = sprintf('%s/%s.*', dirname($file), $filename);
            }
        }

        // sort the patterns
        usort($patterns, function ($a, $b) {
            return strcmp($a, $b);
        });

        // iterate over the patterns and delete the matching files
        foreach ($patterns as $pattern) {
            // iterate over the found files and delete them
            foreach (glob($pattern) as $filename) {
                $this->getFilesystemAdapter()->delete($filename);
            }
        }
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
}
