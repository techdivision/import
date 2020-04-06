<?php

/**
 * TechDivision\Import\Loaders\Sorters\SortStringsAscSorter
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
 * Callback that can be used to sort strings in an ascending order.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
