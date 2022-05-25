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
     * @param $blackList
     * @return array|mixed|string|string[]|null
     */
    public function interpolateQuery($blacklistingEntities, $columnNames, $tableName) {

        foreach ($blacklistingEntities  as $key => $entities) {
            if ($key === $tableName)
            foreach ($entities as $entity => $values){
                foreach ($values as $key => $columnName) {
                    if ($entity === 'general' || $entity === 'insert') {
                        $columnNames = $this->unsetColumnValues($columnNames, $columnName);
                    }
                }
            }
        }
        return $columnNames;
    }

    public function unsetColumnValues($columnNames, $columnName) {
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
        foreach ($blackListings as $key => $entities) {
            // query whether or not black list values for the entity type are available
            if (isset($entities)) {
                $blacklistingEntities = $entities;
            }
        }

        if (isset($blacklistingEntities)) {
            if (array_key_exists($tableName, $blacklistingEntities)) {
                $columnNames = $this->interpolateQuery($blacklistingEntities, $columnNames, $tableName);
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
