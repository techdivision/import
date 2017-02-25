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

use TechDivision\Import\Utils\MemberNames;

/**
 * Repository implementation to load EAV attribute data.
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
     * The prepared statement to load the attributes by the passed is user defined flag.
     *
     * @var \PDOStatement
     */
    protected $eavAttributesByUserDefinedStmt;

    /**
     * The prepared statement to load the attributes for a specifc option value and store ID.
     *
     * @var \PDOStatement
     */
    protected $eavAttributesByOptionValueAndStoreIdStmt;

    /**
     * The prepared statement to load the attributes for a specific entity type and the user defined flag.
     *
     * @var \PDOStatement
     */
    protected $eavAttributesByEntityTypeIdAndUserDefinedStmt;

    /**
     * The prepared statement to load the attributes for a specific entity type ID and name.
     *
     * @var \PDOStatement
     */
    protected $eavAttributesByEntityTypeIdAndAttributeSetNameStmt;

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
        $this->eavAttributesByUserDefinedStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_IS_USER_DEFINED);
        $this->eavAttributesByOptionValueAndStoreIdStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID);
        $this->eavAttributesByEntityTypeIdAndUserDefinedStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_IS_USER_DEFINED);
        $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME);
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

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_ID     => $entityTypeId,
            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetName
        );

        // initialize the array for the EAV attributes
        $eavAttributes = array();

        // execute the prepared statement and return the array with the EAV attributes
        $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt->execute($params);
        foreach ($this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt->fetchAll(\PDO::FETCH_ASSOC) as $eavAttribute) {
            $eavAttributes[$eavAttribute[MemberNames::ATTRIBUTE_CODE]] = $eavAttribute;
        }

        // return the array with the EAV attributes
        return $eavAttributes;
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

        // initialize the params
        $params = array(
            MemberNames::VALUE    => $optionValue,
            MemberNames::STORE_ID => $storeId
        );

        // execute the prepared statement and return the array with the EAV attributes
        $this->eavAttributesByOptionValueAndStoreIdStmt->execute($params);
        return $this->eavAttributesByOptionValueAndStoreIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function findAllByIsUserDefined($isUserDefined = 1)
    {

        // initialize the array for the EAV attributes
        $eavAttributes = array();

        // initialize the params
        $params = array(MemberNames::ID_USER_DEFINED => $isUserDefined);

        // execute the prepared statement and return the array with the EAV attributes
        $this->eavAttributesByUserDefinedStmt->execute($params);
        foreach ($this->eavAttributesByUserDefinedStmt->fetchAll(\PDO::FETCH_ASSOC) as $eavAttribute) {
            $eavAttributes[$eavAttribute[MemberNames::ATTRIBUTE_CODE]] = $eavAttribute;
        }

        // return the array with the EAV attributes
        return $eavAttributes;
    }

    /**
     * Return's an array with the available EAV attributes for the passed is entity type and
     * user defined flag.
     *
     * @param integer $entityTypeId  The entity type ID of the EAV attributes to return
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed entity type and user defined flag
     */
    public function findAllByEntityTypeIdAndIsUserDefined($entityTypeId, $isUserDefined = 1)
    {

        // initialize the array for the EAV attributes
        $eavAttributes = array();

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_ID  => $entityTypeId,
            MemberNames::ID_USER_DEFINED => $isUserDefined
        );

        // execute the prepared statement and return the array with the EAV attributes
        $this->eavAttributesByEntityTypeIdAndUserDefinedStmt->execute($params);
        foreach ($this->eavAttributesByEntityTypeIdAndUserDefinedStmt->fetchAll(\PDO::FETCH_ASSOC) as $eavAttribute) {
            $eavAttributes[$eavAttribute[MemberNames::ATTRIBUTE_CODE]] = $eavAttribute;
        }

        // return the array with the EAV attributes
        return $eavAttributes;
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
        if (sizeof($eavAttributes = $this->findAllByOptionValueAndStoreId($optionValue, $storeId)) > 0) {
            return reset($eavAttributes);
        }
    }
}
