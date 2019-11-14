<?php

/**
 * TechDivision\Import\Repositories\EavAttributeRepositoryInterface
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
 * Interface for a EAV attribute data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavAttributeRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's the EAV attribute with the passed ID.
     *
     * @param string $attributeId The ID of the EAV attribute to return
     *
     * @return array The EAV attribute
     */
    public function load($attributeId);

    /**
     * Return's the EAV attribute with the passed entity type ID and code.
     *
     * @param integer $entityTypeId  The entity type ID of the EAV attributes to return
     * @param string  $attributeCode The code of the EAV attribute to return
     *
     * @return array The EAV attribute
     */
    public function findOneByEntityTypeIdAndAttributeCode($entityTypeId, $attributeCode);

    /**
     * Return's an array with the available EAV attributes for the passed entity type ID and attribute set name.
     *
     * @param integer $entityTypeId     The entity type ID of the EAV attributes to return
     * @param string  $attributeSetName The attribute set name of the EAV attributes to return
     *
     * @return array The array with all available EAV attributes
     */
    public function findAllByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName);

    /**
     * Return's an array with the available EAV attributes for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with all available EAV attributes
     */
    public function findAllByOptionValueAndStoreId($optionValue, $storeId);

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function findAllByIsUserDefined($isUserDefined = 1);

    /**
     * Return's an array with the available EAV attributes for the passed is entity type and
     * user defined flag.
     *
     * @param integer $entityTypeId  The entity type ID of the EAV attributes to return
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed entity type and user defined flag
     */
    public function findAllByEntityTypeIdAndIsUserDefined($entityTypeId, $isUserDefined = 1);

    /**
     * Return's an array with the available EAV attributes for the passed is entity type.
     *
     * @param integer $entityTypeId The entity type ID of the EAV attributes to return
     *
     * @return array The array with the EAV attributes matching the passed entity type
     */
    public function findAllByEntityTypeId($entityTypeId);

    /**
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function findOneByOptionValueAndStoreId($optionValue, $storeId);
}
