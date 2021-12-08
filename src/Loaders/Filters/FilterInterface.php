<?php

/**
 * TechDivision\Import\Loaders\Filters\FilterInterface
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
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilterInterface
{

    /**
     * The filter's unique name.
     *
     * @return string The unique name
     */
    public function getName() : string;

    /**
     * Return's the flag used to define what will be passed to the callback invoked
     * by the `array_filter()` method.
     *
     * @return int The flag
     */
    public function getFlag() : int;
}
