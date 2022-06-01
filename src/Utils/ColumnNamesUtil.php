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
     * @param array $blackListingEntity Blacklist configuration for the entity
     * @param array $columnNames        Column name from database
     * @return array
     */
    public function purgeColumnNames($blackListingEntity, $columnNames)
    {
        if (!isset($blackListingEntity['insert']) && !isset($blackListingEntity['general'])) {
            return $columnNames;
        }

        return array_filter($columnNames, static function ($columnName) use ($blackListingEntity) {
            $isblacklisted = false;
            foreach ($blackListingEntity as $entity => $blackListedColumnNames) {
                if ($entity === 'update') {
                    continue;
                }
                if (in_array($columnName, $blackListedColumnNames)) {
                    $isblacklisted = true;
                    break;
                }
            }
            return !$isblacklisted;
        });
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
        $columnNames = $this->columnNameLoader->load($this->tablePrefixUtil->getPrefixedTableName($tableName));

        // append the columnName For Placeholder in the registry
        $this->tablePrefixUtil->getRegistryProcessor()->mergeAttributesRecursive(
            'columnNames',
            array($tableName => $columnNames)
        );

        // load the blacklist values from the configuration
        $blackListings = $this->tablePrefixUtil->getConfiguration()->getBlackListings();

        if (!isset($blackListings[$tableName])) {
            return implode(',', $columnNames);
        }
        
        // Clean Column Name basic on Blacklisting
        return implode(
            ',',
            $this->purgeColumnNames($blackListings[$tableName], $columnNames)
        );
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
