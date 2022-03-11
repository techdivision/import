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
use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\SqlStatementRepositoryInterface;
use TechDivision\Import\Utils\SanitizerInterface;

/**
 * An abstract processor implementation provide basic CRUD functionality.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractBaseProcessor
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
     * The array holding row data sanitizer.
     *
     * @var \ArrayObject
     */
    protected $sanitizers;

    /**
     * The default statement name.
     *
     * @var string
     */
    protected $defaultStatementName;

    /**
     * Initialize the processor with the passed connection and utility class name, as well as optional sanitizers.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The repository instance
     * @param \ArrayObject                                                      $sanitizers             The array with the sanitizer instances
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        \ArrayObject $sanitizers = null
    ) {

        // pass the connection and the SQL statement repository to the parent class
        parent::__construct($connection, $sqlStatementRepository);

        // set the sanititzes, if available
        $this->setSanitizers($sanitizers ?? new \ArrayObject());
    }

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
     * Gets sanitizers list.
     *
     * @return \ArrayObject The sanitizers
     */
    public function getSanitizers(): \ArrayObject
    {
        return $this->sanitizers;
    }

    /**
     * Sets sanitizers list.
     *
     * @param \ArrayObject $sanitizers The sanitizers
     *
     * @return void
     */
    public function setSanitizers(\ArrayObject $sanitizers): void
    {
        $this->sanitizers = $sanitizers;
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
     * @param string $statement The statement string
     *
     * @return array The prepared row
     */
    protected function prepareRow(array $row, $statement = '')
    {

        // remove the entity status
        unset($row[EntityStatus::MEMBER_NAME]);

        // Remove unused rows from statement
        if (!empty($statement)) {
            foreach ($row as $key => $value) {
                if (!preg_match('/(:'.$key.'[^a-zA-Z_])|(:'.$key.'$)/', $statement)) {
                    unset($row[$key]);
                }
            }
        }

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
        $statement = $this->getPreparedStatement($name, $this->getDefaultStatementName());
        $row = $this->sanitize($row, $statement);

        try {
            // finally execute the prepared statement
            $statement->execute($this->prepareRow($row, $statement->queryString));
        } catch (\PDOException $pdoe) {
            // initialize the SQL statement with the placeholders
            $sql = $statement->queryString;

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
        $this->defaultStatementName = $this->firstKey($statements);

        foreach ($statements as $name => $statement) {
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }
    }

    /**
     * Returns the first key of the passed array.
     *
     * This method has been used instead of the PHP function array_key_first, because
     * this function will be available with PHP >= 7.3.0.
     *
     * @param array $array The array to return the first key for
     *
     * @return mixed|NULL The first key or NULL
     * @link https://www.php.net/array_key_first
     */
    private function firstKey(array $array)
    {

        // load the array keys
        $keys = array_keys($array);

        // try to load and return the first key
        foreach ($keys as $key) {
            return $key;
        }

        // return NULL otherwise
        return null;
    }

    /**
     * Passes row data and statement to sanitizers list.
     *
     * @param array         $row       The row that has to be sanitized
     * @param \PDOStatement $statement The statement that has to be sanitzied
     *
     * @return array The sanitized row
     */
    protected function sanitize(array $row, \PDOStatement $statement) : array
    {

        // load the raw statement that has to be sanitized
        $rawStatement = $statement->queryString;

        // invoke the registered sanitizers on the statement
        /** @var SanitizerInterface $sanitizer */
        foreach ($this->sanitizers as $sanitizer) {
            $row = $sanitizer->execute($row, $rawStatement);
        }

        // return the sanizized row
        return $row;
    }
}
