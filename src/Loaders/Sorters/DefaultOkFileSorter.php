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
     *
     * @param array  $v
     * @param string $k
     *
     * @return boolean TRUE if the value with the actual key should be in the array, else FALSE
     */
    public function __invoke(array $a, array $b) : int
    {

        $countA = sizeof($a);
        $countB = sizeof($b);

        if ($countA === $countB) {
            return 0;
        }

        return ($countB < $countA) ? - 1 : 1;
    }
}