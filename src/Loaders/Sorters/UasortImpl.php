<?php

/**
 * TechDivision\Import\Loaders\Sorters\UasortImpl
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

namespace TechDivision\Import\Loaders\Sorters;

/**
 * Filter implementation that uses PHP's `uasort` method to sort the passed data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
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
     *
     * @param array $data
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