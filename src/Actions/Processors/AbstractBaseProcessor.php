<?php

/**
 * TechDivision\Import\Actions\Processors\AbstractBaseProcessor
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

namespace TechDivision\Import\Actions\Processors;

use TechDivision\Import\Utils\EntityStatus;

/**
 * An abstract processor implementation provide basic CRUD functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractBaseProcessor extends AbstractProcessor
{

    /**
     * The array with the statements to be prepared.
     *
     * @var array
     */
    protected $statements = array();

    /**
     * The array with the prepared statements.
     *
     * @var array
     */
    protected $preparedStatements = array();

    /**
     * Return's the array with the SQL statements that has to be prepared.
     *
     * @return array The SQL statements to be prepared
     */
    protected function getStatements()
    {
        return $this->statements;
    }

    /**
     * Set's the prepared statement.
     *
     * @param \PDOStatement $preparedStatement The prepared statement
     *
     * @return void
     * @deprecated Use TechDivision\Import\Actions\Processors\AbstractBaseProcessor::addPreparedStatement() instead
     */
    protected function setPreparedStatement(\PDOStatement $preparedStatement)
    {
        $this->preparedStatements[$preparedStatement->queryString] = $preparedStatement;
    }

    /**
     * Add's the prepared statement.
     *
     * @param string        $name              The unique name of the prepared statement
     * @param \PDOStatement $preparedStatement The prepared statement
     *
     * @return void
     */
    protected function addPreparedStatement($name, \PDOStatement $preparedStatement)
    {
        $this->preparedStatements[$name] = $preparedStatement;
    }

    /**
     * Return's the prepared statement.
     *
     * @param string $name The name of the prepared statement to return
     *
     * @return \PDOStatement The prepared statement
     */
    protected function getPreparedStatement($name = null)
    {

        // try to load the prepared statement, or use the default one
        if (isset($this->preparedStatements[$name])) {
            return $this->preparedStatements[$name];
        }

        // return the first (default) prepared statement
        return reset($this->preparedStatements);
    }

    /**
     * Qeuery whether or not the prepared statement is available or not.
     *
     * @param string $name The nqme of the prepared statement
     *
     * @return boolean TRUE if the prepared statement is available, else FALSE
     */
    protected function hasPreparedStatement($name)
    {
        return isset($this->preparedStatements[$name]);
    }

    /**
     * The array with the prepared statements.
     *
     * @return array The prepared statments
     */
    protected function getPreparedStatements()
    {
        return $this->preparedStatements;
    }

    /**
     * Prepare's and return's the passed row by removing the
     * entity status.
     *
     * @param array $row The row to prepare
     *
     * @return array The prepared row
     */
    protected function prepareRow(array $row)
    {

        // remove the entity status
        unset($row[EntityStatus::MEMBER_NAME]);

        // return the prepared row
        return $row;
    }

    /**
     * Implements the CRUD functionality the processor is responsible for,
     * can be one of CREATE, READ, UPDATE or DELETE a entity.
     *
     * @param array       $row  The data to handle
     * @param string|null $name The name of the prepared statement to execute
     *
     * @return void
     */
    public function execute($row, $name = null)
    {
        try {
            $this->getPreparedStatement($name)->execute($this->prepareRow($row));
        } catch(\PDOException $pdoe) {
            // prepare the params for more detailed error message
            $params = array();
            foreach ($row as $key => $value) {
                $params[] = $key . ' => ' . $value;
            }

            // prepare the error message itself
            $message = sprintf(
                '%s when executing SQL "%s" with params "[%s]"',
                $pdoe->getMessage(),
                $this->getPreparedStatement($name)->queryString,
                implode(', ', $params)
            );

            // re-throw the exception with a more detailed error message
            throw new \PDOException($message, null, $pdoe);
        }
    }

    /**
     * Initializes the proceessor with the prepared statements.
     *
     * @return void
     */
    public function init()
    {
        foreach ($this->getStatements() as $name => $statement) {
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }
    }
}
