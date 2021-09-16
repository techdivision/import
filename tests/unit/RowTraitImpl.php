<?php

/**
 * TechDivision\Import\RowTraitImpl
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

/**
 * Wrapper for a subject that uses the row trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class RowTraitImpl extends AbstractRowTraitImpl
{

    /**
     * Set's the actual row, that has to be processed.
     *
     * @param array $row The row
     *
     * @return void
     */
    public function setRow(array $row)
    {
        parent::setRow($row);
    }

    /**
     * Query whether or not a value for the column with the passed name exists.
     *
     * @param string $name The column name to query for a valid value
     *
     * @return boolean TRUE if the value is set, else FALSE
     */
    public function hasValue($name)
    {
        return parent::hasValue($name);
    }

    /**
     * Set the value in the passed column name.
     *
     * @param string $name  The column name to set the value for
     * @param mixed  $value The value to set
     *
     * @return void
     */
    public function setValue($name, $value)
    {
        parent::setValue($name, $value);
    }

    /**
     * Resolve's the value with the passed colum name from the actual row. If a callback will
     * be passed, the callback will be invoked with the found value as parameter. If
     * the value is NULL or empty, the default value will be returned.
     *
     * @param string        $name     The name of the column to return the value for
     * @param mixed|null    $default  The default value, that has to be returned, if the row's value is empty
     * @param callable|null $callback The callback that has to be invoked on the value, e. g. to format it
     *
     * @return mixed|null The, almost formatted, value
     */
    public function getValue($name, $default = null, callable $callback = null)
    {
        return parent::getValue($name, $default, $callback);
    }
}
