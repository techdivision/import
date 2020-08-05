<?php

/**
 * TechDivision\Import\Loaders\AttributeGroupNameLoader
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

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for attribute group names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeGroupNameLoader implements LoaderInterface
{

    /**
     * The attribute group names.
     *
     * @var array
     */
    protected $attributeGroupNames = array();

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // load the entity types
        $entityTypes = $importProcessor->getEavEntityTypes();

        // prepare the array with the attribute sets
        foreach ($entityTypes as $entityType) {
            // load the attribute sets for the entity type
            $attributeSets = $importProcessor->getEavAttributeSetsByEntityTypeId($entityType[MemberNames::ENTITY_TYPE_ID]);
            // load the attribute sets
            foreach ($attributeSets as $attributeSet) {
                // load the attribute groups for the attribute set
                $attributeGroups = $importProcessor->getEavAttributeGroupsByAttributeSetId($attributeSet[MemberNames::ATTRIBUTE_SET_ID]);
                // assemble the array with the attribute group names
                foreach ($attributeGroups as $attributeGroup) {
                    $this->attributeGroupNames[$entityType[MemberNames::ENTITY_TYPE_CODE]][$attributeSet[MemberNames::ATTRIBUTE_SET_NAME]][] = $attributeGroup[MemberNames::ATTRIBUTE_GROUP_NAME];
                }
            }
        }
    }

    /**
     * Loads and returns the array with the attribute group names.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->attributeGroupNames;
    }
}
