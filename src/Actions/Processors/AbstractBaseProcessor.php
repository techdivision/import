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
     * The default statement name.
     *
     * @var string
     */
    protected $defaultStatementName;

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
     * Return's the prepared statement with the passed name or the default one.
     *
     * @param string|null $name        The name of the prepared statement to return
     * @param string|null $defaultName The name of the default prepared statement
     *
     * @return \PDOStatement The prepared statement
     */
    protected function getPreparedStatement($name = null, $defaultName = null)
    {

        // try to load the prepared statement, or use the default one
        if (isset($this->preparedStatements[$name])) {
            return $this->preparedStatements[$name];
        }

        // return the default prepared statement
        return $this->preparedStatements[$defaultName];
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
     * Return's the name of the processor's default statement.
     *
     * @return string The statement name
     */
    public function getDefaultStatementName()
    {
        return $this->defaultStatementName;
    }

    /**
     * Implements the CRUD functionality the processor is responsible for,
     * can be one of CREATE, READ, UPDATE or DELETE a entity.
     *
     * @param array       $row                  The row to persist
     * @param string|null $name                 The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return void
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null)
    {
        try {
            // finally execute the prepared statement
            $this->getPreparedStatement($name, $this->getDefaultStatementName())->execute($this->prepareRow($row));
        } catch (\PDOException $pdoe) {
            // initialize the SQL statement with the placeholders
            $sql = $this->getPreparedStatement($name, $this->getDefaultStatementName())->queryString;

            // replace the placeholders with the values
            foreach ($row as $key => $value) {
                $sql = str_replace(sprintf(':%s', $key), $value, $sql);
            }

            // prepare the error message itself
            $message = sprintf('%s when executing SQL "%s"', $pdoe->getMessage(), preg_replace('/\r\n\s\s+/', ' ', $sql));

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

        // load the statements
        $statements = $this->getStatements();

        // initialize the default statement name
        $this->defaultStatementName = array_key_first($statements);

        foreach ($statements as $name => $statement) {
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }
    }
}
