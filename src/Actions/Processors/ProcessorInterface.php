<?php

/**
 * TechDivision\Import\Actions\Processors\ProcessorInterface
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

use TechDivision\Import\Repositories\SqlStatementRepositoryInterface;

/**
 * The interface for all CRUD processor implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Actions\Processors\ProcessorInterface
 */
interface ProcessorInterface
{

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
     * Return's the class name of the SQL repository instance with the SQL statements to use.
     *
     * @return string The SQL repository instance class name
     */
    public function getSqlStatementRepositoryClassName();

    /**
     * Load's the SQL statement with the passed ID from the SQL repository.
     *
     * @param string $id The ID of the SQL statement to load
     *
     * @return string The SQL statement with the passed ID
     */
    public function loadStatement($id);

    /**
     * Initializes the proceessor with the prepared statements.
     *
     * @return void
     */
    public function init();

    /**
     * Return's the name of the processor's default statement.
     *
     * @return string The statement name
     */
    public function getDefaultStatementName();

    /**
     * Persist's the passed row.
     *
     * @param array       $row                  The row to persist
     * @param string|null $name                 The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return void
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null);
}
