<?php

/**
 * TechDivision\Import\Loaders\ColumnMetadataLoader
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Connection\ConnectionInterface;

/**
 * Loader for table column metadata.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnMetadataLoader implements LoaderInterface
{

    /**
     * The column metadata.
     *
     * @var array
     */
    protected $metadata = array();

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Connection\ConnectionInterface $connection The DB connection instance used to load the table metadata
     */
    public function __construct(ConnectionInterface $connection)
    {

        // initialize the statement to load the table columns
        $stmtTables = $connection->query('SHOW TABLES');

        // fetch the tables names of the actual database
        $tables = $stmtTables->fetchAll(\PDO::FETCH_ASSOC);

        // iterate over the rows with the tables
        foreach ($tables as $table) {
            // extract the tablename
            $tableName = reset($table);
            // load the table's columns
            $stmtColumns = $connection->query(sprintf('SHOW COLUMNS FROM %s', $tableName));
            $columns = $stmtColumns->fetchAll(\PDO::FETCH_ASSOC);
            // prepare the metadata
            foreach ($columns as $column) {
                $this->metadata[$tableName][$column['Field']] = $column;
            }
        }
    }

    /**
     * Loads and returns tables metadata.
     *
     * @param string $tableName The table name to return the metadata for
     *
     * @return \ArrayAccess The array with the metadata
     */
    public function load($tableName = null)
    {

        // query whether or not metadata for the table is available
        if (isset($this->metadata[$tableName])) {
            return $this->metadata[$tableName];
        }

        // return an empty array otherwise
        return array();
    }
}
