<?php

/**
 * TechDivision\Import\Loaders\Sorters\DefaultOkFileSorter
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
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DefaultOkFileSorter
{

    /**
     * Compare's that size of the passed arrays.
     *
     * @param array $a The first array
     * @param array $b The second array
     *
     * @return int int < 0 if size of a is less than the size of b; > 0 if the size of a is greater than size of b, and 0 if size of both is equal
     */
    public function __invoke(array $a, array $b) : int
    {

        // count the number of elements
        $countA = sizeof($a);
        $countB = sizeof($b);

        // return 0, if the size is equal
        if ($countA === $countB) {
            return 0;
        }

        // return -1 if th size of a > b, else 1
        return ($countB < $countA) ? - 1 : 1;
    }
}
