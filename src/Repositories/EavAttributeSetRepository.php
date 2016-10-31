<?php

/**
 * TechDivision\Import\Repositories\EavAttributeSetRepository
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class EavAttributeSetRepository extends AbstractRepository
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
        $this->eavAttributeSetStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTE_SET);
        $this->eavAttributeSetsByEntityTypeIdStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID);
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
        return reset($this->eavAttributeSetStmt->fetchAll());
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

        // prepare the array with the attribute set codes as keys
        foreach ($this->eavAttributeSetsByEntityTypeIdStmt->fetchAll() as $eavAttributeSet) {
            $eavAttributeSets[$eavAttributeSet[MemberNames::ATTRIBUTE_SET_NAME]] = $eavAttributeSet;
        }

        // return the array with the attribute sets
        return $eavAttributeSets;
    }
}
