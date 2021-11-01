<?php

/**
 * TechDivision\Import\Utils\ColumnValuesUtil
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface;

/**
 * Utility class for dynamic column value handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnValuesUtil implements ColumnValuesUtilInterface
{

    /**
     * The column name loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $columnNameLoader;

    /**
     * The table prefix utility instance.
     *
     * @var \TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface
     */
    protected $tablePrefixUtil;

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface        $columnNameLoader The column name loader instance
     * @param \TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface $tablePrefixUtil  The table prefix utility instance
     */
    public function __construct(LoaderInterface $columnNameLoader, TablePrefixUtilInterface $tablePrefixUtil)
    {
        $this->columnNameLoader = $columnNameLoader;
        $this->tablePrefixUtil = $tablePrefixUtil;
    }

    /**
     * Returns a concatenated list with key => value pairs of the passed table.
     *
     * @param string $tableName The table name to return the list for
     *
     * @return string The concatenated list with the key => value pairs
     */
    public function getColumnValues($tableName)
    {

        // initialize the array for the column key => value pairs
        $columnValues = array();

        // load the column names from the loader
        $columnNames = $this->columnNameLoader->load($this->tablePrefixUtil->getPrefixedTableName($tableName));

        // load and append the column key => value pairs to the array
        foreach ($columnNames as $columnName) {
            $columnValues[] = sprintf('%s=:%s', $columnName, $columnName);
        }

        // implode and return the column values
        return implode(',', $columnValues);
    }

    /**
     * Compiles the passed SQL statement.
     *
     * @param string $statement The SQL statement to compile
     *
     * @return string The compiled SQL statement
     */
    public function compile($statement)
    {
        return preg_replace_callback(sprintf('/\$\{%s:(.*)\}/U', ColumnValuesUtilInterface::TOKEN), function (array $matches) {
            return $this->getColumnValues($matches[1]);
        }, $statement);
    }
}
