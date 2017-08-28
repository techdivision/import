<?php

/**
 * TechDivision\Import\Repositories\AbstractRepository
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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\SqlStatementsInterface;
use TechDivision\Import\Connection\ConnectionInterface;

/**
 * An abstract respository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractRepository implements RepositoryInterface
{

    /**
     * The connection instance.
     * .
     * @var \TechDivision\Import\Connection\ConnectionInterface;
     */
    protected $connection;

    /**
     * The utility class instance with the SQL statements to use.
     *
     * @var \TechDivision\Import\Utils\SqlStatementsInterface
     */
    protected $utilityClass;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface $connection   The connection instance
     * @param \TechDivision\Import\Utils\SqlStatementsInterface   $utilityClass The utility class instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementsInterface $utilityClass
    ) {

        // set the passed instances
        $this->setConnection($connection);
        $this->setUtilityClass($utilityClass);

        // initialize the instance
        $this->init();
    }

    /**
     * Set's the connection to use.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface $connection The connection instance
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection to use.
     *
     * @return \TechDivision\Import\Connection\ConnectionInterface The connection instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set's the utility class instance with the SQL statements to use.
     *
     * @param \TechDivision\Import\Utils\SqlStatementsInterface $utilityClass The utility class instance
     *
     * @return void
     */
    public function setUtilityClass(SqlStatementsInterface $utilityClass)
    {
        $this->utilityClass = $utilityClass;
    }

    /**
     * Return's the utility class instance with the SQL statements to use.
     *
     * @return \TechDivision\Import\Utils\SqlStatementsInterface The utility class instance
     */
    public function getUtilityClass()
    {
        return $this->utilityClass;
    }

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @return string The utility class name
     */
    public function getUtilityClassName()
    {
        return get_class($this->getUtilityClass());
    }
}
