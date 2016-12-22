<?php

/**
 * TechDivision\Import\Actions\Processors\Batch\AbstractBatchBaseProcessor
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

namespace TechDivision\Import\Actions\Processors\Batch;

use TechDivision\Import\Actions\Processors\AbstractProcessor;

/**
 * An abstract batch processor implementation provide basic CRUD functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractBatchBaseProcessor extends AbstractProcessor
{

    /**
     * The stack with the data to import.
     *
     * @var array
     */
    protected $stack = array();

    /**
     * The destructor that finally executes the batch processing.
     */
    public function __destruct()
    {
        if ($this->getStackSize() > 0) {
            $this->prepareAndExecute();
        }
    }

    /**
     * Return's the stack with the rows to be persisted.
     *
     * @return array The stack with the rows to persist
     */
    protected function getStack()
    {
        return $this->stack;
    }

    /**
     * Return's the number of rows on the stack.
     *
     * @return intger The stack size
     */
    protected function getStackSize()
    {
        return sizeof($this->getStack());
    }

    /**
     * Add's the passed row to the stack.
     *
     * @param array $row The row to be persisted
     *
     * @return void
     */
    protected function addToStack($row)
    {
        $this->stack[] = $row;
    }

    /**
     * Initialize's the prepared statement according to the number
     * of found rows on the stack.
     *
     * @return string The statement to be prepared
     */
    protected function initStatement()
    {

        // setup the placeholders - a fancy way to make the long "(?, ?, ?)..." string
        $rowPlaces = '(' . implode(', ', array_fill(0, $this->getNumberOfPlaceholders(), '?')) . ')';
        $allPlaces = implode(', ', array_fill(0, $this->getStackSize() - 1, $rowPlaces));

        // append the placeholders the the statement
        return sprintf('%s, %s', $this->getStatement(), $allPlaces);
    }

    /**
     * Initialize's the parameters from the stack and return's
     * them as one big array.
     *
     * @return array The array with the initialized parameters
     */
    protected function initParams()
    {

        // initialize the array for the parameters
        $params = array();

        // merge the parameters from the stack => we need one big array
        foreach ($this->getStack() as $row) {
            $params = array_merge($params, $row);
        }

        // return the merged params
        return $params;
    }

    /**
     * Prepare's and execute's the prepared statement to
     * insert the all rows on the stack.
     *
     * @return void
     */
    protected function prepareAndExecute()
    {

        // initialize params and statement
        $params = $this->initParams();
        $statement = $this->initStatement();

        // load the connection
        $connection = $this->getConnection();

        // prepare the statement and execute it
        $preparedStatement = $connection->prepare($statement);
        $preparedStatement->execute($params);
    }

    /**
     * The number of placeholders of the prepared statement.
     *
     * @return integer The number of placeholers
     */
    abstract protected function getNumberOfPlaceholders();

    /**
     * Return's the SQL statement that has to be prepared.
     *
     * @return string The SQL statement
     */
    abstract protected function getStatement();

    /**
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return void
     */
    public function execute($row)
    {
        $this->addToStack($row);
    }

    /**
     * Initializes the proceessor with the prepared statements.
     *
     * @return void
     */
    public function init()
    {
    }
}
