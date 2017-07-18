<?php

/**
 * TechDivision\Import\Connection\PDOConnectionWrapper
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

namespace TechDivision\Import\Connection;

/**
 * A wrapper for a \PDO connection instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PDOConnectionWrapper implements ConnectionInterface
{

    /**
     * The \PDO connection that has to be wrapped.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * Initializes the wrapper with the passed PDO connection.
     *
     * @param \PDO $connection The PDO connection to wrap
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via
     * the instance are not committed until you end the transaction by calling commit().
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call
     * to beginTransaction() starts a new transaction.
     *
     * @return void
     * @throws \PDOException Throws a PDOException if there is no active transaction.
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rolls back the current transaction, as initiated by beginTransaction().
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @throws \PDOException Throws a PDOException if there is no active transaction
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Fetch the SQLSTATE associated with  the last operation on the database handle.
     *
     * @return mixed The error code of the last operation of the database handle
     * @link http://php.net/manual/en/pdo.errorcode.php
     */
    public function errorCode()
    {
        return $this->connection->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle.
     *
     * @return array Returns an array of error information about the last operation performed by this database handle
     * @link http://php.net/manual/en/pdo.errorinfo.php
     */
    public function errorInfo()
    {
        return $this->connection->errorInfo();
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     *
     * @param string $statement The SQL statement to prepare and execute
     *
     * @return \PDOStatement|boolean Returns a \PDOStatement object, or FALSE on failure
     * @link http://php.net/manual/en/pdo.query.php
     */
    public function query($statement)
    {
        return call_user_func_array(array($this->connection, 'query'), func_get_args());
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $statement     This must be a valid SQL statement template for the target database server
     * @param array  $driverOptions This array holds one or more key=>value pairs to set attribute values for the \PDOStatement object that this method returns
     *
     * @return \PDOStatement If the database server successfully prepares the statement, this method returns a \PDOStatement object
     * @throws \PDOException Throws a PDOException if the database server cannot successfully prepare the statement
     * @link http://php.net/manual/en/pdo.prepare.php
     */
    public function prepare($statement, array $driverOptions = array())
    {
        return $this->connection->prepare($statement, $driverOptions);
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     *
     * @param string $statement The SQL statement to prepare and execute
     *
     * @return integer The number of affected rows
     * @link http://php.net/manual/en/pdo.exec.php
     */
    public function exec($statement)
    {
        return $this->connection->exec($statement);
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $name Name of the sequence object from which the ID should be returned
     *
     * @return string A string representing the row ID of the last row that was inserted into the database
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     */
    public function lastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string  $string        The string to be quoted
     * @param integer $parameterType Provides a data type hint for drivers that have alternate quoting styles
     *
     * @return string|boolean Returns a quoted string that is theoretically safe to pass into an SQL statement or FALSE if the driver does not support quoting in this way
     * @link http://php.net/manual/en/pdo.quote.php
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        return $this->connection->quote($string, $parameterType);
    }
}
