<?php

/**
 * TechDivision\Import\Observers\DynamicAttributeLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

/**
 * Loader implementation that dynamically creates an array with columns from the CSV file that contains values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DynamicAttributeLoader implements AttributeLoaderInterface
{

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * Second parameter has  to be an associative array with the member name as key
     * and an array that contains the column name and the data's backend type, e. g.
     *
     * <code>array('min_qty => array('out_of_stock_qty', 'float'))</code>
     *
     * @param \TechDivision\Import\Observers\DynamicAttributeObserverInterface $observer The observer to load the attributes for
     * @param array                                                            $columns  The array with the possible columns to load the data from
     *
     * @return array The prepared attributes
     * @throws \Exception Is thrown, if the size of the option values doesn't equals the size of swatch values, in case
     */
    public function load(DynamicAttributeObserverInterface $observer, array $columns)
    {

        // initialize the array for the attributes
        $attr = array();

        // iterate over the possible columns and handle the data
        foreach ($columns as $memberName => $metadata) {
            // extract column name and backend type from the metadata
            list ($columnName, $backendType) = $metadata;
            // query whether or not, the column is available in the CSV file
            if ($observer->hasHeader($columnName)) {
                // query whether or not a column contains a value
                if ($observer->hasValue($columnName)) {
                    $attr[$memberName] = $observer->castValueByBackendType($backendType, $observer->getValue($columnName));
                }
            }
        }

        // return the prepared product
        return $attr;
    }
}
