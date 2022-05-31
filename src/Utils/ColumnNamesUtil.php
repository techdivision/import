<?php

/**
 * TechDivision\Import\Utils\ColumnNamesUtil
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-customer
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface;

/**
 * Utility class for dynamic column name handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-customer
 * @link      http://www.techdivision.com
 */
class ColumnNamesUtil implements ColumnNamesUtiInterface
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
     * @param \TechDivision\Import\Loaders\LoaderInterface             $columnNameLoader The column name loader instance
     * @param \TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface $tablePrefixUtil  The table prefix utility instance
     */
    public function __construct(LoaderInterface $columnNameLoader, TablePrefixUtilInterface $tablePrefixUtil)
    {
        $this->columnNameLoader = $columnNameLoader;
        $this->tablePrefixUtil = $tablePrefixUtil;
    }

    /**
     * @param array  $blacklistingEntities Blacklist configuration for the entity
     * @param string $columnNames          Column Name
     * @param string $tableName            Table Name
     * @return array
     */
    public function purgeColumnNames($blacklistingEntities, $columnNames, $tableName)
    {
        if (isset($blacklistingEntities[$tableName])) {
            foreach ($blacklistingEntities[$tableName] as $entity => $values) {
                foreach ($values as $key => $columnName) {
                    if (in_array($entity, ['general', 'insert'], true)) {
                        $columnNames =  str_replace(','. $columnName, '', $columnNames);
                    }
                }
            }
        }
        return $columnNames;
    }

    /**
     * Returns a concatenated list with column names of the passed table.
     *
     * @param string $tableName The table name to return the list for
     *
     * @return string The concatenated list of column names
     */
    public function getColumnNames($tableName)
    {
        return implode(',', $this->columnNameLoader->load($this->tablePrefixUtil->getPrefixedTableName($tableName)));
    }

    /**
     * @param string $tableName Table Name of entity
     * @return string
     */
    public function getColumnFinaleNames($tableName)
    {
        // load the blacklist values from the configuration
        $blackListings = $this->tablePrefixUtil->getConfiguration()->getBlackListings();

        $columnNames = $this->getColumnNames($tableName);

        if (is_array($blackListings[0]) && !empty($blackListings[0])) {
            if (array_key_exists($tableName, $blackListings[0])) {
                return $this->purgeColumnNames($blackListings[0], $columnNames, $tableName);
            }
        }
        return $columnNames;
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
        return preg_replace_callback(sprintf('/\$\{%s:(.*)\}/U', ColumnNamesUtiInterface::TOKEN), function (array $matches) {
            return $this->getColumnFinaleNames($matches[1]);
        }, $statement);
    }
}
