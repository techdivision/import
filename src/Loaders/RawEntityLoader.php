<?php

/**
 * TechDivision\Import\Loaders\RawEntityLoader
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for raw entities.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RawEntityLoader implements LoaderInterface
{

    /**
     * The connection instance.
     *
     * @var \TechDivision\Import\Dbal\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The column metadata loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $rawEntities = array();

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface $connection           The DB connection instance used to load the table metadata
     * @param \TechDivision\Import\Loaders\LoaderInterface             $columnMetadataLoader The column metadata loader instance
     * @param \TechDivision\Import\Services\ImportProcessorInterface   $importProcessor      The import processor instance
     */
    public function __construct(
        ConnectionInterface $connection,
        LoaderInterface $columnMetadataLoader,
        ImportProcessorInterface $importProcessor
    ) {

        // set the connection
        $this->connection = $connection;

        // load the available EAV entity types
        $eavEntityTypes = $importProcessor->getEavEntityTypes();

        // iterate over the entity types and create the raw entities
        foreach ($eavEntityTypes as $eavEntityType) {
            // load the columns from the metadata
            $columns = array_filter(
                $columnMetadataLoader->load($eavEntityType[MemberNames::ENTITY_TABLE]),
                function ($value) {
                    return $value['Key'] !== 'PRI' && $value['Null'] === 'NO' ;
                }
            );
            // initialize the raw entities and their default values, if available
            foreach ($columns as $column) {
                $this->rawEntities[$eavEntityType[MemberNames::ENTITY_TYPE_CODE]][$column['Field']] = $this->loadDefaultValue($column);
            }
        }
    }

    /**
     * Return's the default value for the passed column.
     *
     * @param array $column The column to return the default value for
     *
     * @return string|null The default value for the passed column
     */
    protected function loadDefaultValue(array $column)
    {

        // load the default value
        $default = $column['Default'];

        // if a default value has been found
        if ($default === null) {
            // if the column don't allowed NULL and of type text or varchar, we have to return an empty string
            if (isset($column['Null'], $column['Type'])
                && $column['Null'] === "NO"
                && ($column['Type'] === 'text' || strpos($column['Type'], 'varchar') === 0)
            ) {
                return '';
            }
            return $default;
        }

        try {
            // try to load it resolve it by executing a select statement (assuming it is an MySQL expression)
            $row = $this->connection->query(sprintf('SELECT %s()', $default))->fetch(\PDO::FETCH_ASSOC);
            return reset($row);
        } catch (\PDOException $pdoe) {
        }

        // return the default value
        return $default;
    }

    /**
     * Loads and returns data.
     *
     * @param string|null $entityTypeCode The table name to return the list for
     * @param array       $data           An array with data that will be used to initialize the raw entity with
     *
     * @return \ArrayAccess The array with the raw data
     */
    public function load($entityTypeCode = null, array $data = array())
    {
        return isset($this->rawEntities[$entityTypeCode]) ? array_merge($this->rawEntities[$entityTypeCode], $data) : $data;
    }
}
