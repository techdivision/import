<?php

/**
 * TechDivision\Import\Repositories\EavAttributeRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load EAV attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeRepository extends AbstractRepository implements EavAttributeRepositoryInterface
{

    /**
     * The prepared statement to load the attribute by the passed attribute ID.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeStmt;

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
     * The prepared statement to load a attribute by its entity type ID and name.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeByEntityTypeIdAndAttributeCodeStmt;

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

        // initialize the prepared statements
        $this->eavAttributeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE));
        $this->eavAttributesByUserDefinedStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTES_BY_IS_USER_DEFINED));
        $this->eavAttributesByOptionValueAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID));
        $this->eavAttributeByEntityTypeIdAndAttributeCodeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE));
        $this->eavAttributesByEntityTypeIdAndUserDefinedStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_IS_USER_DEFINED));
        $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME));
    }

    /**
     * Return's the EAV attribute with the passed ID.
     *
     * @param string $attributeId The ID of the EAV attribute to return
     *
     * @return array The EAV attribute
     */
    public function load($attributeId)
    {
        // execute the prepared statement and return the EAV attribute with the passed entity type ID and code
        $this->eavAttributeStmt->execute(array(MemberNames::ATTRIBUTE_ID => $attributeId));
        return $this->eavAttributeStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the EAV attribute with the passed entity type ID and code.
     *
     * @param integer $entityTypeId  The entity type ID of the EAV attributes to return
     * @param string  $attributeCode The code of the EAV attribute to return
     *
     * @return array The EAV attribute
     */
    public function findOneByEntityTypeIdAndAttributeCode($entityTypeId, $attributeCode)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_ID => $entityTypeId,
            MemberNames::ATTRIBUTE_CODE => $attributeCode
        );

        // execute the prepared statement and return the EAV attribute with the passed entity type ID and code
        $this->eavAttributeByEntityTypeIdAndAttributeCodeStmt->execute($params);
        return $this->eavAttributeByEntityTypeIdAndAttributeCodeStmt->fetch(\PDO::FETCH_ASSOC);
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

        // load the available EAV attributes
        $availableEavAttributes = $this->eavAttributesByEntityTypeIdAndAttributeSetNameStmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($availableEavAttributes as $eavAttribute) {
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
    public function findAllByOptionValueAndStoreId($optionValue, $storeId): array
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

        // load the available EAV attributes
        $availableEavAttributes = $this->eavAttributesByUserDefinedStmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($availableEavAttributes as $eavAttribute) {
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

        // load the available EAV attributes
        $availableEavAttributes = $this->eavAttributesByEntityTypeIdAndUserDefinedStmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($availableEavAttributes as $eavAttribute) {
            $eavAttributes[$eavAttribute[MemberNames::ATTRIBUTE_CODE]] = $eavAttribute;
        }

        // return the array with the EAV attributes
        return $eavAttributes;
    }

    /**
     * Return's an array with the available EAV attributes for the passed is entity type.
     *
     * @param integer $entityTypeId The entity type ID of the EAV attributes to return
     *
     * @return array The array with the EAV attributes matching the passed entity type
     */
    public function findAllByEntityTypeId($entityTypeId)
    {

        // initialize the array for the EAV attributes
        $eavAttributes = array();

        // execute the prepared statement and return the array with the EAV attributes
        $this->eavAttributesByEntityTypeIdAndUserDefinedStmt->execute(array(MemberNames::ENTITY_TYPE_ID  => $entityTypeId));

        // load the available EAV attributes
        $availableEavAttributes = $this->eavAttributesByEntityTypeIdAndUserDefinedStmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($availableEavAttributes as $eavAttribute) {
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
     */
    public function findOneByOptionValueAndStoreId($optionValue, $storeId)
    {

        // execute the prepared statement and return the array with the fail EAV attributes
        if (sizeof($eavAttributes = $this->findAllByOptionValueAndStoreId($optionValue, $storeId)) > 0) {
            return reset($eavAttributes);
        }
        return [];
    }
}
