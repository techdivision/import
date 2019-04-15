<?php

/**
 * TechDivision\Import\RowTrait
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

/**
 * A trait that provides row handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait RowTrait
{

    /**
     * The actual row, that has to be processed.
     *
     * @var array
     */
    protected $row = array();

    /**
     * The flag that stop's overserver execution on the actual row.
     *
     * @var boolean
     */
    protected $skipRow = false;

    /**
     * Stop's observer execution on the actual row.
     *
     * @return void
     */
    public function skipRow($skip = true)
    {
        $this->skipRow = $skip;
    }

    /**
     * Query's whether or not the observer execution for the given row has to be skipped.
     *
     * @return boolean TRUE if the observer execution has to be skipped, else FALSE
     */
    public function rowHasToBeSkipped()
    {
        return $this->skipRow;
    }

    /**
     * Set's the actual row, that has to be processed.
     *
     * @param array $row The row
     *
     * @return void
     */
    public function setRow(array $row)
    {
        $this->row = $row;
    }

    /**
     * Return's the actual row.
     *
     * @return array The actual row
     */
    public function getRow()
    {
        return $this->row;
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

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the key for the row
            $headerValue = $this->getHeader($name);

            // query whether the rows column has a vaild value
            return (isset($this->row[$headerValue]) && $this->row[$headerValue] !== '');
        }

        // return FALSE if not
        return false;
    }

    /**
     * Set the value in the passed column name.
     *
     * @param string $name  The column name to set the value for
     * @param mixed  $value The value to set
     *
     * @return void
     */
    protected function setValue($name, $value)
    {
        $this->row[$this->getHeader($name)] = $value;
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

        // initialize the value
        $value = null;

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the header value
            $headerValue = $this->getHeader($name);
            // query wheter or not, the value with the requested key is available
            if ((isset($this->row[$headerValue]) && $this->row[$headerValue] !== '')) {
                $value = $this->row[$headerValue];
            }
        }

        // query whether or not, a callback has been passed
        if ($value != null && is_callable($callback)) {
            $value = call_user_func($callback, $value);
        }

        // query whether or not
        if ($value == null && $default !== null) {
            $value = $default;
        }

        // return the value
        return $value;
    }
}
