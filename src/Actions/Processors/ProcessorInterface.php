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

/**
 * The interface for all CRUD processor implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
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
     * Return's the utility class instance with the SQL statements to use.
     *
     * @return \TechDivision\Import\Utils\SqlStatementsInterface The utility class instance
     */
    public function getUtilityClass();

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @return string The utility class name
     */
    public function getUtilityClassName();

    /**
     * Initializes the proceessor with the prepared statements.
     *
     * @return void
     */
    public function init();

    /**
     * Persist's the passed row.
     *
     * @param array       $row  The row to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function execute($row, $name = null);
}
