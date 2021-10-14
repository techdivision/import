<?php

/**
 * TechDivision\Import\Loaders\PregMatchFilteredLoader
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

use TechDivision\Import\Loaders\Filters\FilterInterface;
use TechDivision\Import\Loaders\Filters\PregMatchFilterInterface;

/**
 * Generic loader implementation that uses a glob compatible pattern
 * to load files from a given directory.
 *
 * The loader uses the PHP function `array_filter` to filter the files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/array_filter
 */
class PregMatchFilteredLoader implements PregMatchFilteredLoaderInterface
{

    /**
     * The parent loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    private $loader;

    /**
     * Construct that initializes the loader with the parent loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader The parent loader instance
     */
    public function __construct(FilteredLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Return's the parent loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The parent loader instance
     */
    protected function getLoader() : FilteredLoaderInterface
    {
        return $this->loader;
    }

    /**
     * Return's the number of matches found.
     *
     * @return int The number of matches
     */
    public function countMatches() : int
    {
        return sizeof($this->getMatches());
    }

    /**
     * Return's the matches of all filters.
     *
     * @return array The array with the matches
     */
    public function getMatches() : array
    {

        // initialize the array for the matches
        $matches = array();

        // load the filters for their matches
        $filters = $this->getFilters();

        // append the filters matches to the array
        foreach ($filters as $filter) {
            if ($filter instanceof PregMatchFilterInterface) {
                foreach ($filter->getMatches() as $match) {
                    $matches[] = $match;
                }
            }
        }

        // return the matches of all filters
        return $matches;
    }

    /**
     * Reset's the registered filters.
     *
     * @return void
     */
    public function reset() : void
    {

        // load the filters that has to be resetted
        $filters = $this->getFilters();

        // reset the filters
        foreach ($filters as $filter) {
            if ($filter instanceof PregMatchFilterInterface) {
                $filter->reset();
            }
        }
    }

    /**
     * Return's the value of the key with the passed name out of the matches
     * with the passed key.
     *
     * @param string      $name The name of the match with the given key that has to be to returned
     * @param string|null $key  The key of the match to return the value for
     *
     * @return string The match itself
     * @throws \InvalidArgumentException Is thrown the value of the match with the passed name and key is not available
     */
    public function getMatch(string $name, string $key = null) : string
    {

        // load the matches from all filters
        $matches = $this->getMatches();

        // use the passed key or the key of the last match
        $key = $key ?? sizeof($matches) - 1;

        // query whether or not a match is available
        if (isset($matches[$key][$name])) {
            return $matches[$key][$name];
        }

        // is thrown, if the value can not be loaded
        throw new \InvalidArgumentException(sprintf('Can\'t load match with key "%s" and name "%s"', $key, $name));
    }

    /**
    * Return's the size of the unfiltered entries.
    *
    * @return int The size of the found entries before the filters have been applied
    */
    public function getSizeBeforeFiltersHaveBeenApplied() : int
    {
        return $this->getLoader()->getSizeBeforeFiltersHaveBeenApplied();
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
        $this->getLoader()->addFilter($filter);
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
        return $this->getLoader()->hasFilter($name);
    }

    /**
     * Return's the filter with the passed name.
     *
     * @param string $name The name of the filter to be returned
     *
     * @return \TechDivision\Import\Loaders\Filters\PregMatchFilterInterface  The filter with the requested name
     * @throws \InvalidArgumentException Is thrown, if the requested filter is not available
     */
    public function getFilter(string $name) : FilterInterface
    {
        return $this->getLoader()->getFilter($name);
    }

    /**
     * Return's the array with the filter callbacks.
     *
     * @return \TechDivision\Import\Loaders\Filters\PregMatchFilterInterface[] The filter callbacks
     */
    public function getFilters() : array
    {
        return $this->getLoader()->getFilters();
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
        return $this->getLoader()->load($pattern);
    }
}
