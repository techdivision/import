<?php

/**
 * TechDivision\Import\Loaders\Filters\FilterImplInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders\Filters;

/**
 * Interface for filter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilterImplInterface
{

    /**
     * Add's the passed filter to the loader instance.
     *
     * @param \TechDivision\Import\Loaders\Filters\FilterInterface $filter The filter to add
     *
     * @return void
     */
    public function addFilter(FilterInterface $filter) : void;

    /**
     * Query's whether or not the loader has a filter with the passed name.
     *
     * @param string $name The name of the filter to query for
     *
     * @return bool TRUE if the filter is available, else FALSE
     */
    public function hasFilter(string $name) : bool;

    /**
     * Return's the filter with the passed name.
     *
     * @param string $name The name of the filter to be returned
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterInterface The filter with the requested name
     * @throws \InvalidArgumentException Is thrown, if the requested filter is not available
     */
    public function getFilter(string $name) : FilterInterface;

    /**
     * Return's the array with the filter callbacks.
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterInterface[] The filter callbacks
     */
    public function getFilters() : array;

    /**
     * The methods that filters by applying the registered filters on it.
     *
     * @param array $data The array with the data that has to be filtered
     *
     * @return void
     */
    public function filter(array &$data) : void;
}
