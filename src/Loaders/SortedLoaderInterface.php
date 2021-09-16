<?php

/**
 * TechDivision\Import\Loaders\SortedLoaderInterface
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

/**
 * Interface for sorted loader implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/uasort
 */
interface SortedLoaderInterface extends LoaderInterface
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
}
