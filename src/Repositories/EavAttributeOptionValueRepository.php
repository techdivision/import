<?php

/**
 * TechDivision\Import\Repositories\EavAttributeOptionValueCachedRepository
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Cached repository implementation to load EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueRepository extends AbstractCachedRepository implements EavAttributeOptionValueRepositoryInterface
{

    /**
     * The prepared statement to load the existing EAV attribute option values.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeOptionValuesStmt;

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
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->eavAttributeOptionValuesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUES));

        // initialize the prepared statements
        $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID));

        // initialize the prepared statements
        $this->eavAttributeOptionValueByAttributeCodeAndStoreIdAndValueStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE));
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
        $cacheKey = $this->cacheKey(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID, $params);

        // query whether or not the result has been cached
        if ($this->notCached($cacheKey)) {
            // load and return the EAV attribute option value with the passed parameters
            $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt->execute($params);
            $this->toCache($cacheKey, $this->eavAttributeOptionValueByOptionIdAndStoreIdStmt->fetch(\PDO::FETCH_ASSOC));
        }

        // return the value from the cache
        return $this->fromCache($cacheKey);
    }

    /**
     * Load's and return's the EAV attribute option value with the passed code, store ID and value.
     *
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function findOneByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value)
    {

        // the parameters of the EAV attribute option to load
        $params = array(
            MemberNames::ATTRIBUTE_CODE => $attributeCode,
            MemberNames::STORE_ID       => $storeId,
            MemberNames::VALUE          => $value
        );

        // prepare the cache key
        $cacheKey = $this->cacheKey(SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE, $params);

        // query whether or not the result has been cached
        if ($this->notCached($cacheKey)) {
            // load and return the EAV attribute option value with the passed parameters
            $this->eavAttributeOptionValueByAttributeCodeAndStoreIdAndValueStmt->execute($params);
            $this->toCache($cacheKey, $this->eavAttributeOptionValueByAttributeCodeAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC));
        }

        // return the value from the cache
        return $this->fromCache($cacheKey);
    }
}
