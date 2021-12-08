<?php

/**
 * TechDivision\Import\Loaders\Sorters\SortStringsAscSorter
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
 * Callback that can be used to sort strings in an ascending order.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://php.net/strcmp
 */
class SortStringsAscSorter
{

    /**
     * Compare's that passed strings binary safe and return's an integer, depending on the comparison result.
     *
     * @param string $str1 The first string
     * @param string $str2 The second string
     *
     * @return int int < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
     */
    public function __invoke(string $str1, string $str2) : int
    {
        return strcmp($str1, $str2);
    }
}
