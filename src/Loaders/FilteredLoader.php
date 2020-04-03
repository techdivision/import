<?php

/**
 * TechDivision\Import\Loaders\FilteredLoader
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Loaders\Filters\FilterInterface;
use TechDivision\Import\Loaders\Filters\ArrayFilterImpl;
use TechDivision\Import\Loaders\Filters\FilterImplInterface;

/**
 * Generic loader implementation that uses a glob compatible pattern
 * to load files from a given directory.
 *
 * The loader uses the PHP function `array_filter` to filter the files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/array_filter
 */
class FilteredLoader implements FilteredLoaderInterface
{

    /**
     * The parent loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    private $loader;

    /**
     * The size of the entries before the filters have been applied.
     *
     * @var int
     */
    private $sizeBeforeFiltersHaveBeenApplied = 0;

    /**
     * The filter instance to use.
     *
     * @var \TechDivision\Import\Loaders\Filters\FilterImplInterface
     */
    private $filterImpl;

    /**
     * Construct that initializes the loader with the parent loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface                  $loader The parent loader instance
     * @param \TechDivision\Import\Loaders\Filters\FilterImplInterface|null $filterImpl The filter instance to use
     */
    public function __construct(LoaderInterface $loader, FilterImplInterface $filterImpl = null)
    {
        $this->loader = $loader;
        $this->filterImpl = $filterImpl ?? new ArrayFilterImpl();
    }

    /**
     * Return's the loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader() : LoaderInterface
    {
        return $this->loader;
    }

    /**
     * Return's the filter instance.
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterImplInterface The filter instance
     */
    protected function getFilterImpl() : FilterImplInterface
    {
        return $this->filterImpl;
    }

    /**
     * Set's the size of the unfiltered entries.
     *
     * @param int $izeBeforeFiltersHaveBeenApplied The size of the entries before the filters have been applied
     *
     * @return void
     */
    protected function setSizeBeforeFiltersHaveBeenApplied(int $izeBeforeFiltersHaveBeenApplied) : void
    {
        $this->sizeBeforeFiltersHaveBeenApplied = $izeBeforeFiltersHaveBeenApplied;
    }

    /**
     * Return's the size of the unfiltered entries.
     *
     * @return int The size of the found entries before the filters have been applied
     */
    public function getSizeBeforeFiltersHaveBeenApplied() : int
    {
        return $this->sizeBeforeFiltersHaveBeenApplied;
    }

    /**
     * Loads, sorts and returns the files by using the passed glob pattern.
     *
     * If no pattern will be passed to the `load()` method, the files of
     * the actual directory using `getcwd()` will be returned.
     *
     * @param string|null $pattern The pattern to load the files from the filesystem
     *
     * @return array The array with the data
     */
    public function load(string $pattern = null) : array
    {

        // load the data from the parent loader
        $this->setSizeBeforeFiltersHaveBeenApplied(sizeof($data = $this->getLoader()->load($pattern)));

        // filter them by the given filters
        $this->getFilterImpl()->filter($data);

        // return the sorted data
        return $data;
    }

    /**
     * Add's the passed filter to the loader instance.
     *
     * @param \TechDivision\Import\Loaders\Filters\FilterInterface $filter The filter to add
     *
     * @return void
     */
    public function addFilter(FilterInterface $filter) : void
    {
        $this->getFilterImpl()->addFilter($filter);
    }

    /**
     * Query's whether or not the loader has a filter with the passed name.
     *
     * @param string $name The name of the filter to query for
     *
     * @return bool TRUE if the filter is available, else FALSE
     */
    public function hasFilter(string $name) : bool
    {
        return $this->getFilterImpl()->hasFilter($name);
    }

    /**
     * Return's the filter with the passed name.
     *
     * @param string $name The name of the filter to be returned
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterInterface The filter with the requested name
     * @throws \InvalidArgumentException Is thrown, if the requested filter is not available
     */
    public function getFilter(string $name) : FilterInterface
    {
        return $this->getFilterImpl()->getFilter($name);
    }

    /**
     * Return's the array with the filter callbacks.
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterInterface[] The filter callbacks
     */
    public function getFilters() : array
    {
        return $this->getFilterImpl()->getFilters();
    }
}
