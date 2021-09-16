<?php

/**
 * TechDivision\Import\Repositories\EavAttributeSetRepositoryInterface
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

/**
 * Interface for a EAV attribute set data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavAttributeSetRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's the EAV attribute set with the passed ID.
     *
     * @param integer $id The EAV attribute set ID
     *
     * @return array The attribute set
     */
    public function load($id);

    /**
     * Return's the attribute sets for the passed entity type ID, whereas the array
     * is prepared with the attribute set names as keys.
     *
     * @param mixed $entityTypeId The entity type ID to return the attribute sets for
     *
     * @return array|boolean The attribute sets for the passed entity type ID
     */
    public function findAllByEntityTypeId($entityTypeId);

    /**
     * Load's and return's the EAV attribute set with the passed entity type ID and attribute set name.
     *
     * @param string $entityTypeId     The entity type ID of the EAV attribute set to load
     * @param string $attributeSetName The attribute set name of the EAV attribute set to return
     *
     * @return array The EAV attribute set
     */
    public function findOneByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName);

    /**
     * Load's and return's the EAV attribute set with the passed entity type code and attribute set name.
     *
     * @param string $entityTypeCode   The entity type code of the EAV attribute set to load
     * @param string $attributeSetName The attribute set name of the EAV attribute set to return
     *
     * @return array The EAV attribute set
     */
    public function findOneByEntityTypeCodeAndAttributeSetName($entityTypeCode, $attributeSetName);
}
