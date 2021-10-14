<?php

/**
 * TechDivision\Import\Loaders\Filters\UasortImpl
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
 * Filter implementation that uses PHP's `array_filter` method to filter the passed data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://php.net/array_filter
 */
class ArrayFilterImpl implements FilterImplInterface
{

    /**
     * An array with callables that can be used as callbacks for the array_filter() method.
     *
     * @var \TechDivision\Import\Loaders\Filters\FilterInterface[]
     */
    private $filters = array();

    /**
     * Add's the passed filter to the loader instance.
     *
     * @param \TechDivision\Import\Loaders\Filters\FilterInterface $filter The filter to add
     *
     * @return void
     */
    public function addFilter(FilterInterface $filter) : void
    {
        $this->filters[$filter->getName()] = $filter;
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
        return isset($this->filters[$name]);
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

        // query whether or not the requested filter exists
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }

        // throw an exception, if not
        throw new \InvalidArgumentException(sprintf('Can\'t find filter "%s" registered in loader "%s"', $name, __CLASS__));
    }

    /**
     * Return's the array with the filter callbacks.
     *
     * @return \TechDivision\Import\Loaders\Filters\FilterInterface[] The filter callbacks
     */
    public function getFilters() : array
    {
        return $this->filters;
    }

    /**
     * The methods that filters the passed array by inovking the `array_filter` method
     * for each of the registered filter callbacks on it.
     *
     * @param array $data The array with the data that has to be filtered
     *
     * @return void
     */
    public function filter(array &$data) : void
    {
        // filter them by the given filters
        foreach ($this->getFilters() as $filter) {
            $data = array_filter($data, $filter, $filter->getFlag());
        }
    }
}
