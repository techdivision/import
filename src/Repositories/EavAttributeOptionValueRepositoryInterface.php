<?php

/**
 * TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface
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

/**
 * The interface for a repository that loads EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavAttributeOptionValueRepositoryInterface extends RepositoryInterface
{

    /**
     * THe cache adapter instance used to warm the repository.
     *
     * @return \TechDivision\Import\Cache\CacheAdapterInterface The repository's cache adapter instance
     */
    public function getCacheAdapter();

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName();

    /**
     * Load's and return's the available EAV attribute option values.
     *
     * @return array The EAV attribute option values
     */
    public function findAll();

    /**
     * Load's and return's the available EAV attribute option values by the passed entity type and store ID.
     *
     * @param integer $entityTypeId The entity type ID of the attribute option values to return
     * @param integer $storeId      The store ID of the attribute option values to return
     *
     * @return array The EAV attribute option values
     */
    public function findAllByEntityTypeIdAndStoreId($entityTypeId, $storeId);

    /**
     * Load's and return's the EAV attribute option value with the passed option ID and store ID
     *
     * @param string  $optionId The option ID of the attribute option
     * @param integer $storeId  The store ID of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function findOneByOptionIdAndStoreId($optionId, $storeId);

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
    public function findOneByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value);
}
