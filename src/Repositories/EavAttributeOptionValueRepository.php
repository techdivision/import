<?php

/**
 * TechDivision\Import\Repositories\EavAttributeOptionValueRepository
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface;
use TechDivision\Import\Dbal\Repositories\AbstractRepository;

/**
 * Cached repository implementation to load cached EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueRepository extends AbstractRepository implements EavAttributeOptionValueRepositoryInterface
{

    /**
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * The prepared statement to load the existing EAV attribute option values.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValuesStmt;

    /**
     * The prepared statement to load the existing EAV attribute option values by their entity type and store ID.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValuesByEntityTypeIdAndStoreIdStmt;

    /**
     * The prepared statement to load an existing EAV attribute option value by its option id and store ID
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValueByOptionIdAndStoreIdStmt;

    /**
     * The prepared statement to load an existing EAV attribute option value by its attribute code, store ID and value.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValueByAttributeCodeAndStoreIdAndValueStmt;

    /**
     * The prepared statement to load an existing EAV attribute option value by its entity type ID, attribute code, store ID and value.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValueStmt;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     * @param \TechDivision\Import\Cache\CacheAdapterInterface                       $cacheAdapter           The cache adapter instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        CacheAdapterInterface $cacheAdapter
    ) {

        // pass the connection the SQL statement repository to the parent class
        parent::__construct($connection, $sqlStatementRepository);

        // set the cache adapter instance
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->eavAttributeOptionValuesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUES));
        $this->eavAttributeOptionValuesByEntityTypeIdAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUES_BY_ENTITY_TYPE_ID_AND_STORE_ID));
        $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID));
        $this->eavAttributeOptionValueByAttributeCodeAndStoreIdAndValueStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE));
        $this->eavAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValueStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE));
    }

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::VALUE_ID;
    }

    /**
     * Returns the cache adapter instance used to warm the repository.
     *
     * @return \TechDivision\Import\Cache\CacheAdapterInterface The repository's cache adapter instance
     */
    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }

    /**
     * Load's and return's the available EAV attribute option values.
     *
     * @return array The EAV attribute option values
     */
    public function findAll()
    {

        // load and return all available EAV attribute option values
        $this->eavAttributeOptionValuesStmt->execute(array());
        return $this->eavAttributeOptionValuesStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the available EAV attribute option values by the passed entity type and store ID.
     *
     * @param integer $entityTypeId The entity type ID of the attribute option values to return
     * @param integer $storeId      The store ID of the attribute option values to return
     *
     * @return array The EAV attribute option values
     */
    public function findAllByEntityTypeIdAndStoreId($entityTypeId, $storeId)
    {

        // the parameters of the EAV attribute option to load
        $params = array(
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::STORE_ID       => $storeId,
        );

        // load and return all available EAV attribute option values by the passed params
        $this->eavAttributeOptionValuesByEntityTypeIdAndStoreIdStmt->execute($params);

        // fetch the values and return them
        while ($record = $this->eavAttributeOptionValuesByEntityTypeIdAndStoreIdStmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    /**
     * Load's and return's the EAV attribute option value with the passed option ID and store ID
     *
     * @param string  $optionId The option ID of the attribute option
     * @param integer $storeId  The store ID of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function findOneByOptionIdAndStoreId($optionId, $storeId)
    {

        // the parameters of the EAV attribute option to load
        $params = array(
            MemberNames::OPTION_ID => $optionId,
            MemberNames::STORE_ID  => $storeId,
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID => $params));

        // query whether or not the item has already been cached
        if ($this->cacheAdapter->isCached($cacheKey)) {
            return $this->cacheAdapter->fromCache($cacheKey);
        }

        // load the EAV attribute option value with the passed parameters
        $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt->execute($params);

        // query whether or not the EAV attribute option value is available in the database
        if ($eavAttributeOptionValue = $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::EAV_ATTRIBUTE_OPTION_VALUE => $eavAttributeOptionValue[$this->getPrimaryKeyName()]);
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($uniqueKey, $eavAttributeOptionValue, array($cacheKey => $uniqueKey));
        }

        // finally, return it
        return $eavAttributeOptionValue;
    }

    /**
     * Load's and return's the EAV attribute option value with the passed entity type ID, code, store ID and value.
     *
     * @param string  $entityTypeId  The entity type ID of the EAV attribute to load the option value for
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function findOneByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value)
    {

        // the parameters of the EAV attribute option to load
        $params = array(
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::ATTRIBUTE_CODE => $attributeCode,
            MemberNames::STORE_ID       => $storeId,
            MemberNames::VALUE          => $value
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE => $params));

        // return the cached result if available
        if ($this->cacheAdapter->isCached($cacheKey)) {
            return $this->cacheAdapter->fromCache($cacheKey);
        }

        // load the EAV attribute option value with the passed parameters
        $this->eavAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValueStmt->execute($params);

        // query whether or not the result has been cached
        if ($eavAttributeOptionValue = $this->eavAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::EAV_ATTRIBUTE_OPTION_VALUE => $eavAttributeOptionValue[$this->getPrimaryKeyName()]);
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($uniqueKey, $eavAttributeOptionValue, array($cacheKey => $uniqueKey));
        }

        // finally, return it
        return $eavAttributeOptionValue;
    }
}
