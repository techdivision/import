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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

/**
 * The interface for a repository that loads EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavAttributeOptionValueRepositoryInterface extends CachedRepositoryInterface
{

    /**
     * Load's and return's the available EAV attribute option values.
     *
     * @return array The EAV attribute option values
     */
    public function findAll();

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
     * Load's and return's the EAV attribute option value with the passed code, store ID and value.
     *
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     * @deprecated Since 2.0.2, because multiple attributes with the same attribute code, but differenct entity type code can be available
     * @see \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface::findOneByEntityTypeIdAndAttributeCodeAndStoreIdAndValue()
     */
    public function findOneByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value);

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
