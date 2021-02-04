<?php

/**
 * TechDivision\Import\Repositories\RepositoryInterface
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

/**
 * The respository interface.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Repositories\RepositoryInterface
 */
interface RepositoryInterface
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init();

    /**
     * Return's the connection to use.
     *
     * @return \TechDivision\Import\Connection\ConnectionInterface The connection instance
     */
    public function getConnection();

    /**
     * Set's the repository instance with the SQL statements to use.
     *
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The repository instance
     *
     * @return void
     */
    public function setSqlStatementRepository(SqlStatementRepositoryInterface $sqlStatementRepository);

    /**
     * Return's the repository instance with the SQL statements to use.
     *
     * @return \TechDivision\Import\Repositories\SqlStatementRepositoryInterface The repository instance
     */
    public function getSqlStatementRepository();

    /**
     * Load's the SQL statement with the passed ID from the SQL repository.
     *
     * @param string $id The ID of the SQL statement to load
     *
     * @return string The SQL statement with the passed ID
     */
    public function loadStatement($id);
}
