<?php

/**
 * TechDivision\Import\Observers\CleanUpEmptyColumnsTrait
 *
 * PHP version 7
 *
 * @author    Martin Eissenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
 * @license   https://opensource.org/licenses/MIT
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
    protected $cleanUpEmptyColumnKeys = null;

    /**
     * The array with the default column values.
     *
     * @var array
     */
    protected $defaultColumnValues;

    /**
     * @return array
     */
    public function getCleanUpEmptyColumnKeys(): ?array
    {
        return $this->cleanUpEmptyColumnKeys;
    }

    /**
     * @return array
     */
    public function getDefaultColumnValues(): array
    {
        return $this->defaultColumnValues;
    }

    /**
     * @return string
     */
    protected function getEmptyAttributeValueConstant()
    {
        return $this->getSubject()->getConfiguration()->getConfiguration()->getEmptyAttributeValueConstant();
    }

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


        // initialize the array with the default column values
        $this->defaultColumnValues = array();

        // iterate over the default column values to figure out whether or not the column exists
        $defaultColumnValues = $this->getSubject()->getDefaultColumnValues();

        // prepare the array with the default column values, BUT we only take
        // care of default columns WITHOUT any value, because in only in this
        // case the default EAV value from the DB should be used when a empty
        // column value has been found to create a NEW attribute value
        foreach ($defaultColumnValues as $columnName => $defaultColumnValue) {
            if ($defaultColumnValue === '') {
                $this->defaultColumnValues[$columnName] = $this->getHeader($columnName);
            }
        }

        $emptyValueDefinition = $this->getEmptyAttributeValueConstant();
        // load the header keys
        $headers = in_array($emptyValueDefinition, $this->row, true) ? array_flip($this->getHeaders()) : [];
        // remove all the empty values from the row, expected the columns has to be cleaned-up
        foreach ($this->row as $key => $value) {
            // query whether or not to cleanup complete attribute
            if ($value === $emptyValueDefinition) {
                $this->cleanUpEmptyColumnKeys[$headers[$key]] = $key;
                $this->row[$key] = '';
            }
            // query whether or not the value is empty AND the column has NOT to be cleaned-up
            if (($value === null || $value === '') &&
                in_array($key, $this->cleanUpEmptyColumnKeys) === false &&
                in_array($key, $this->defaultColumnValues) === false
            ) {
                unset($this->row[$key]);
            }
        }

        // finally return the clean row
        return $this->row;
    }
}
