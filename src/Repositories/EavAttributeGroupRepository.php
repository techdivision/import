<?php

/**
 * TechDivision\Import\Repositories\EavAttributeGroupRepository
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
use TechDivision\Import\Dbal\Repositories\AbstractRepository;

/**
 * Repository implementation to load EAV attribute group data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeGroupRepository extends AbstractRepository implements EavAttributeGroupRepositoryInterface
{

    /**
     * The prepared statement to load a specific attribute group.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeGroupStmt;

    /**
     * The prepared statement to load an attribute group for a specific attribute set ID.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeGroupsByAttributeSetIdStmt;

    /**
     * The prepared statement to load an attribute group for the given entity type code, attribute set + attribute group name.
     *
     * @var \PDOStatement
     */
    protected $eavAttributeGroupByEntityTypeCodeAndAttributeSetNameAndAttributeGroupNameStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->eavAttributeGroupStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_GROUP));
        $this->eavAttributeGroupsByAttributeSetIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_GROUPS_BY_ATTRIBUTE_SET_ID));
        $this->eavAttributeGroupByEntityTypeCodeAndAttributeSetNameAndAttributeGroupNameStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ATTRIBUTE_GROUP_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME_AND_ATTRIBUTE_GROUP_NAME));
    }

    /**
     * Return's the EAV attribute group with the passed ID.
     *
     * @param integer $id The EAV attribute group ID
     *
     * @return array The EAV attribute group
     */
    public function load($id)
    {

        // execute the prepared statement and return the EAV attribute group with the passed ID
        $this->eavAttributeGroupStmt->execute(array($id));
        return $this->eavAttributeGroupStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the attribute groups for the passed attribute set ID, whereas the array
     * is prepared with the attribute group names as keys.
     *
     * @param mixed $attributeSetId The EAV attribute set ID to return the attribute groups for
     *
     * @return array|boolean The EAV attribute groups for the passed attribute ID
     */
    public function findAllByAttributeSetId($attributeSetId)
    {

        // initialize the array for the EAV attribute sets
        $eavAttributeGroups = array();

        // load the attributes
        $this->eavAttributeGroupsByAttributeSetIdStmt->execute(array(MemberNames::ATTRIBUTE_SET_ID => $attributeSetId));

        // load the available EAV attribute option groups
        $availableEavAttributeGroups = $this->eavAttributeGroupsByAttributeSetIdStmt->fetchAll(\PDO::FETCH_ASSOC);

        // prepare the array with the attribute group names as keys
        foreach ($availableEavAttributeGroups as $eavAttributeGroup) {
            $eavAttributeGroups[$eavAttributeGroup[MemberNames::ATTRIBUTE_GROUP_NAME]] = $eavAttributeGroup;
        }

        // return the array with the EAV attribute groups
        return $eavAttributeGroups;
    }

    /**
     * Return's the EAV attribute group with the passed entity type code, attribute set and attribute group name.
     *
     * @param string $entityTypeCode     The entity type code of the EAV attribute group to return
     * @param string $attributeSetName   The attribute set name of the EAV attribute group to return
     * @param string $attributeGroupName The attribute group name of the EAV attribute group to return
     *
     * @return array The EAV attribute group
     */
    public function findOneByEntityTypeCodeAndAttributeSetNameAndAttributeGroupName($entityTypeCode, $attributeSetName, $attributeGroupName)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE_CODE     => $entityTypeCode,
            MemberNames::ATTRIBUTE_SET_NAME   => $attributeSetName,
            MemberNames::ATTRIBUTE_GROUP_NAME => $attributeGroupName
        );

        // execute the prepared statement and return the EAV attribute group with the passed params
        $this->eavAttributeGroupByEntityTypeCodeAndAttributeSetNameAndAttributeGroupNameStmt->execute($params);
        return $this->eavAttributeGroupByEntityTypeCodeAndAttributeSetNameAndAttributeGroupNameStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
