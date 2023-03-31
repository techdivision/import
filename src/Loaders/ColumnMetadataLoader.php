<?php

/**
 * TechDivision\Import\Loaders\ColumnMetadataLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface;

/**
 * Loader for table column metadata.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The table prefix utility instance.
     *
     * @var \TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface|null
     */
    protected $tablePrefixUtil;

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface $connection The DB connection instance used to load the table metadata
     */
    public function __construct(ConnectionInterface $connection, TablePrefixUtilInterface $tablePrefixUtil = null)
    {
        // use in load function for backword compatibility
        $this->tablePrefixUtil = $tablePrefixUtil;

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

        // query whether or not metadata for the table is available with table prefix
        if ($this->tablePrefixUtil && isset($this->metadata[$this->tablePrefixUtil->getPrefixedTableName($tableName)])) {
            return $this->metadata[$this->tablePrefixUtil->getPrefixedTableName($tableName)];
        }

        // return an empty array otherwise
        return array();
    }
}
