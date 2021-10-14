<?php

/**
 * TechDivision\Import\Observers\DynamicAttributeObserverInterface
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
 * interface for all state detector aware observer implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface StateDetectorAwareObserverInterface
{

    /**
     * Returns an array of the columns with their types to detect state.
     *
     * @return array The array with the column names as key and their type as value
     */
    public function getColumns();
}
