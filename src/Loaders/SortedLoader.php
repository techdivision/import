<?php

/**
 * TechDivision\Import\Loaders\SortedFilesystemLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Loaders\Sorters\UasortImpl;
use TechDivision\Import\Loaders\Sorters\SorterImplInterface;

/**
 * Generic loader implementation that uses a glob compatible pattern
 * to load files from a given directory.
 *
 * The loader uses the PHP function `uasort` to sort the files, so take
 * into account, that the keys of the array will be kept after sorting.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/uasort
 */
class SortedLoader implements SortedLoaderInterface
{

    /**
     * The parent loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    private $loader;

    /**
     * The sorter instance to use.
     *
     * @var \TechDivision\Import\Loaders\Sorters\SorterImplInterface
     */
    private $sorterImpl;

    /**
     * Construct that initializes the loader with the parent loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface                  $loader     The parent loader instance
     * @param \TechDivision\Import\Loaders\Sorters\SorterImplInterface|null $sorterImpl The sorter instance to use
     */
    public function __construct(LoaderInterface $loader, SorterImplInterface $sorterImpl = null)
    {
        $this->loader = $loader;
        $this->sorterImpl = $sorterImpl ?? new UasortImpl();
    }

    /**
     * Add's the passed sorter to the loader instance.
     *
     * @param callable $sorter The sorter to add
     *
     * @return void
     */
    public function addSorter(callable $sorter) : void
    {
        $this->getSorterImpl()->addSorter($sorter);
    }

    /**
     * Return's the array with the sorter callbacks.
     *
     * @return callable[] The sorter callbacks
     */
    public function getSorters() : array
    {
        return $this->getSorterImpl()->getSorters();
    }

    /**
     * Loads, sorts and returns the files by using the passed glob pattern.
     *
     * If no pattern will be passed to the `load()` method, the files of
     * the actual directory using `getcwd()` will be returned.
     *
     * @param string|null $pattern The pattern to load the files from the filesystem
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(string $pattern = null) : \ArrayAccess
    {

        // sort the files loaded by the parent loader instance
        $this->getSorterImpl()->sort($data = $this->getLoader()->load($pattern));

        // return the sorted files
        return $data;
    }

    /**
     * Return's the sorter instance to use.
     *
     * @return \TechDivision\Import\Loaders\Sorters\SorterImplInterface The sorter instance to use
     */
    protected function getSorterImpl() : SorterImplInterface
    {
        return $this->sorterImpl;
    }

    /**
     * Return's the parent loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The parent loader instance
     */
    protected function getLoader() : LoaderInterface
    {
        return $this->loader;
    }
}
