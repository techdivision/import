<?php

/**
 * TechDivision\Import\Loaders\Sorters\UasortImpl
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders\Sorters;

/**
 * Filter implementation that uses PHP's `uasort` method to sort the passed data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://php.net/uasort
 */
class UasortImpl implements SorterImplInterface
{

    /**
     * An array with callables that can be used as callbacks for the uasort() method.
     *
     * @var callable[]
     */
    private $sorters = array();

    /**
     * Add's the passed sorter to the loader instance.
     *
     * @param callable $sorter The sorter to add
     *
     * @return void
     */
    public function addSorter(callable $sorter) : void
    {
        $this->sorters[] = $sorter;
    }

    /**
     * Return's the array with the sorter callbacks.
     *
     * @return callable[] The sorter callbacks
     */
    public function getSorters() : array
    {
        return $this->sorters;
    }

    /**
     * Sorts the passed array with data by applying each of the registered callbacks
     * by invoking the `uasort` function on it.
     *
     * @param array $data The array to be sorted
     *
     * @return void
     */
    public function sort(array &$data) : void
    {
        // sort them in the order given by the sorters
        foreach ($this->getSorters() as $sorter) {
            uasort($data, $sorter);
        }
    }
}
