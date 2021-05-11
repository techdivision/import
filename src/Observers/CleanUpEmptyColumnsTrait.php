<?php

/**
 * TechDivision\Import\Observers\CleanUpEmptyColumnsTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Martin Eissenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface;
use TechDivision\Import\Utils\ConfigurationKeys;

/**
 * Observer that extracts the missing attribute option values from a customer CSV.
 *
 * @author    Martin Eissenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-customer-attribute
 * @link      http://www.techdivision.com
 */
trait CleanUpEmptyColumnsTrait
{
    /**
     * The array with the column keys that has to be cleaned up when their values are empty.
     *
     * @var array
     */
    protected $cleanUpEmptyColumnKeys;

    /**
     * Remove all the empty values from the row and return the cleared row.
     *
     * @return array The cleared row
     */
    protected function clearRow()
    {

        // query whether or not the column keys has been initialized
        if ($this->cleanUpEmptyColumnKeys === null) {
            // initialize the array with the column keys that has to be cleaned-up
            $this->cleanUpEmptyColumnKeys = array();

            // query whether or not column names that has to be cleaned up have been configured
            if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                && ($this->getSubject() instanceof CleanUpColumnsSubjectInterface)) {
                // if yes, load the column names
                $cleanUpEmptyColumns = $this->getSubject()->getCleanUpColumns();

                // translate the column names into column keys
                foreach ($cleanUpEmptyColumns as $cleanUpEmptyColumn) {
                    if ($this->hasHeader($cleanUpEmptyColumn)) {
                        $this->cleanUpEmptyColumnKeys[] = $this->getHeader($cleanUpEmptyColumn);
                    }
                }
            }
        }

        // remove all the empty values from the row, expected the columns has to be cleaned-up
        foreach ($this->row as $key => $value) {
            // query whether or not the value is empty AND the column has NOT to be cleaned-up
            if (($value === null || $value === '') && in_array($key, $this->cleanUpEmptyColumnKeys) === false) {
                unset($this->row[$key]);
            }
        }

        // finally return the clean row
        return $this->row;
    }
}
