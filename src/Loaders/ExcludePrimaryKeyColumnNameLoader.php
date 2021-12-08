<?php

/**
 * TechDivision\Import\Loaders\ExcludePrimaryKeyColumnNameLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Loader for table column names that excludes the primary key columns.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ExcludePrimaryKeyColumnNameLoader implements LoaderInterface
{

    /**
     * The column metadata loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $columnMetadataLoader;

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $columnMetadataLoader The column metadata loader instance
     */
    public function __construct(LoaderInterface $columnMetadataLoader)
    {
        $this->columnMetadataLoader = $columnMetadataLoader;
    }

    /**
     * Loads and returns data.
     *
     * @param string $tableName The table name to return the list for
     *
     * @return \ArrayAccess The array with the data
     */
    public function load($tableName = null)
    {

        // load the raw column metadata
        $columns = $this->columnMetadataLoader->load($tableName);

        // exclude the primary key columns and return the column names only
        return array_keys(array_filter($columns, function ($value) {
            return !($value['Key'] === 'PRI' && $value['Extra'] === 'auto_increment');
        }));
    }
}
