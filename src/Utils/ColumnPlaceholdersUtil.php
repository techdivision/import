<?php

/**
 * TechDivision\Import\Utils\ColumnPlaceholdersUtil
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
 * Utility class for dynamic column placeholder handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnPlaceholdersUtil implements ColumnPlaceholdersUtiInterface
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
     * @param array  $columnNames          Column Name for value
     * @param string $tableName            Table Name
     * @return array
     */
    public function purgeColumnNames($blacklistingEntities, $columnNames, $tableName)
    {
        if (isset($blacklistingEntities[$tableName])) {
            foreach ($blacklistingEntities[$tableName] as $entity => $values) {
                foreach ($values as $key => $columnName) {
                    if (in_array($entity, ['general', 'insert'], true)) {
                        $columnNames = $this->unsetColumnNames($columnNames, $columnName);
                    }
                }
            }
        }
        return $columnNames;
    }

    /**
     * @param array  $columnNames Column names as array
     * @param string $columnName  Column name
     * @return array
     */
    public function unsetColumnNames($columnNames, $columnName)
    {
        foreach ($columnNames as $key => $values) {
            if ($columnNames[$key] === $columnName) {
                unset($columnNames[$key]);
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
    public function getColumnPlaceholders($tableName)
    {
        // load the column names from the loader
        $columnNames = $this->columnNameLoader->load($this->tablePrefixUtil->getPrefixedTableName($tableName));

        // load the blacklist values from the configuration
        $blackListings =  $this->tablePrefixUtil->getConfiguration()->getBlackListings();
        
        if (is_array($blackListings[0]) && !empty($blackListings[0])) {
            if (array_key_exists($tableName, $blackListings[0])) {
                $columnNames = $this->purgeColumnNames($blackListings[0], $columnNames, $tableName);
            }
        }
        
        // add the double colon (:) for the placeholder
        array_walk($columnNames, function (&$value) {
            $value = sprintf(':%s', $value);
        });

        // implode and return the column names
        return implode(',', $columnNames);
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
        return preg_replace_callback(sprintf('/\$\{%s:(.*)\}/U', ColumnPlaceholdersUtiInterface::TOKEN), function (array $matches) {
            return $this->getColumnPlaceholders($matches[1]);
        }, $statement);
    }
}
