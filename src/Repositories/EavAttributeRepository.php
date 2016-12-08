<?php

/**
 * TechDivision\Import\Repositories\EavAttributeRepository
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
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeRepository extends AbstractRepository
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME);
        $this->eavAttributesByOptionValueAndStoreIdStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID);
    }

    /**
     * Return's an array with the available EAV attributes for the passed entity type ID and attribute set name.
     *
     * @param integer $entityTypeId     The entity type ID of the EAV attributes to return
     * @param string  $attributeSetName The attribute set name of the EAV attributes to return
     *
     * @return array The array with all available EAV attributes
     */
    public function findAllByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName)
    {

        // execute the prepared statement and return the array with the fail EAV attributes
        $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt->execute(array($entityTypeId, $attributeSetName));
        return $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt->fetchAll();
    }

    /**
     * Return's an array with the available EAV attributes for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with all available EAV attributes
     */
    public function findAllByOptionValueAndStoreId($optionValue, $storeId)
    {

        // execute the prepared statement and return the array with the fail EAV attributes
        $this->eavAttributesByOptionValueAndStoreIdStmt->execute(array($optionValue, $storeId));
        return $this->eavAttributesByOptionValueAndStoreIdStmt->fetchAll();
    }

    /**
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     * @see \Importer\Csv\Repositories\Pdo\EavAttributeRepository::findAllByOptionValueAndStoreId()
     */
    public function findOneByOptionValueAndStoreId($optionValue, $storeId)
    {

        // execute the prepared statement and return the array with the fail EAV attributes
        return reset($this->findAllByOptionValueAndStoreId($optionValue, $storeId));
    }
}
