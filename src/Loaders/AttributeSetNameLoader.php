<?php

/**
 * TechDivision\Import\Loaders\AttributeSetNameLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for attribute set names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeSetNameLoader implements LoaderInterface
{

    /**
     * The attribute set names.
     *
     * @var array
     */
    protected $attributeSetNames = array();

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
            foreach ($importProcessor->getEavAttributeSetsByEntityTypeId($entityType[MemberNames::ENTITY_TYPE_ID]) as $attributeSet) {
                $this->attributeSetNames[$entityType[MemberNames::ENTITY_TYPE_CODE]][] = $attributeSet[MemberNames::ATTRIBUTE_SET_NAME];
            }
        }
    }

    /**
     * Loads and returns the array with the attribute set names.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->attributeSetNames;
    }
}
