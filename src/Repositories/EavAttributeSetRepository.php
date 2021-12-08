<?php

/**
 * TechDivision\Import\Repositories\EavAttributeSetRepository
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
 * Repository implementation to load EAV attribute set data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeSetRepository extends AbstractRepository implements EavAttributeSetRepositoryInterface
{

    /**
     * The prepared statement to load a specific attribute set.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeSetStmt;

    /**
     * The prepared statement to load an attribute set for a specific entity type ID.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeSetsByEntityTypeIdStmt;

    /**
     * The prepared statement to load an attribute set with the passed entity type ID and attribute set name.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeSetByEntityTypeIdAndAttributeSetNameStmt;

    /**
     * The prepared statement to load an attribute set with the passed entity type code and attribute set name.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeSetByEntityTypeCodeAndAttributeSetNameStmt;


    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->eavAttributeSetStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_SET));
        $this->eavAttributeSetsByEntityTypeIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID));
        $this->eavAttributeSetByEntityTypeIdAndAttributeSetNameStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME));
        $this->eavAttributeSetByEntityTypeCodeAndAttributeSetNameStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME));
    }

    /**
     * Return's the EAV attribute set with the passed ID.
     *
     * @param integer $id The EAV attribute set ID
     *
     * @return array The attribute set
     */
    public function load($id)
    {

        // execute the prepared statement and return the EAV attribute set with the passed ID
        $this->eavAttributeSetStmt->execute(array($id));
        return $this->eavAttributeSetStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the attribute sets for the passed entity type ID, whereas the array
     * is prepared with the attribute set names as keys.
     *
     * @param mixed $entityTypeId The entity type ID to return the attribute sets for
     *
     * @return array|boolean The attribute sets for the passed entity type ID
     */
    public function findAllByEntityTypeId($entityTypeId)
    {

        // initialize the array for the attribute sets
        $eavAttributeSets = array();

        // load the attributes
        $this->eavAttributeSetsByEntityTypeIdStmt->execute(array($entityTypeId));

        // load the available EAV attribute sets
        $availableEavAttributeSets = $this->eavAttributeSetsByEntityTypeIdStmt->fetchAll(\PDO::FETCH_ASSOC);

        // prepare the array with the attribute set names as keys
        foreach ($availableEavAttributeSets as $eavAttributeSet) {
            $eavAttributeSets[$eavAttributeSet[MemberNames::ATTRIBUTE_SET_NAME]] = $eavAttributeSet;
        }

        // return the array with the attribute sets
        return $eavAttributeSets;
    }

    /**
     * Load's and return's the EAV attribute set with the passed entity type ID and attribute set name.
     *
     * @param string $entityTypeId     The entity type ID of the EAV attribute set to load
     * @param string $attributeSetName The attribute set name of the EAV attribute set to return
     *
     * @return array The EAV attribute set
     */
    public function findOneByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_ID     => $entityTypeId,
            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetName
        );

        // load and return the attribute set
        $this->eavAttributeSetByEntityTypeIdAndAttributeSetNameStmt->execute($params);
        return $this->eavAttributeSetByEntityTypeIdAndAttributeSetNameStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the EAV attribute set with the passed entity type code and attribute set name.
     *
     * @param string $entityTypeCode   The entity type code of the EAV attribute set to load
     * @param string $attributeSetName The attribute set name of the EAV attribute set to return
     *
     * @return array The EAV attribute set
     */
    public function findOneByEntityTypeCodeAndAttributeSetName($entityTypeCode, $attributeSetName)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_CODE   => $entityTypeCode,
            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetName
        );

        // load and return the attribute set
        $this->eavAttributeSetByEntityTypeCodeAndAttributeSetNameStmt->execute($params);
        return $this->eavAttributeSetByEntityTypeCodeAndAttributeSetNameStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
