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
     * The repository instance with the SQL statements to use.
     *
     * @var \TechDivision\Import\Repositories\SqlStatementRepositoryInterface
     */
    protected $sqlStatementRepository;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository
    ) {

        // set the passed instances
        $this->setConnection($connection);
        $this->setSqlStatementRepository($sqlStatementRepository);

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
     * Set's the repository instance with the SQL statements to use.
     *
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The repository instance
     *
     * @return void
     */
    public function setSqlStatementRepository(SqlStatementRepositoryInterface $sqlStatementRepository)
    {
        $this->sqlStatementRepository = $sqlStatementRepository;
    }

    /**
     * Return's the repository instance with the SQL statements to use.
     *
     * @return \TechDivision\Import\Repositories\SqlStatementRepositoryInterface The repository instance
     */
    public function getSqlStatementRepository()
    {
        return $this->sqlStatementRepository;
    }

    /**
     * Load's the SQL statement with the passed ID from the SQL repository.
     *
     * @param string $id The ID of the SQL statement to load
     *
     * @return string The SQL statement with the passed ID
     */
    public function loadStatement($id)
    {
        return $this->getSqlStatementRepository()->load($id);
    }
}
