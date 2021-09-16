<?php

/**
 * TechDivision\Import\Loaders\ColumnNameLoader
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
 * Loader for table column names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnNameLoader implements LoaderInterface
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
        return array_keys($this->columnMetadataLoader->load($tableName));
    }
}
