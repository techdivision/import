<?php

/**
 * TechDivision\Import\Loaders\Sorters\SorterImplInterface
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
 * Interface for sorter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface SorterImplInterface
{

    /**
     * Add's the passed sorter to the loader instance.
     *
     * @param callable $sorter The sorter to add
     *
     * @return void
     */
    public function addSorter(callable $sorter) : void;

    /**
     * Return's the array with the sorter callbacks.
     *
     * @return callable[] The sorter callbacks
     */
    public function getSorters() : array;

    /**
     * Sorts the passed array with data by applying the registered sorters on it.
     *
     * @param array $data The array to be sorted
     *
     * @return void
     */
    public function sort(array &$data) : void;
}
