<?php

/**
 * TechDivision\Import\Loaders\AttributeSetLoader
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
use TechDivision\Import\Configuration\ParamsConfigurationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for attribute sets.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeSetLoader implements LoaderInterface
{

    /**
     * The attribute sets.
     *
     * @var array
     */
    protected $attributeSets = array();

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
                $this->attributeSets[$entityType[MemberNames::ENTITY_TYPE_CODE]][] = $attributeSet[MemberNames::ATTRIBUTE_SET_NAME];
            }
        }
    }

    /**
     * Loads and returns data the custom validation data.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the validations from
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(SubjectConfigurationInterface $configuration = null)
    {

        $entityTypeCode = $configuration->getExecutionContext()->getEntityTypeCode();

        if (isset($this->attributeSets[$entityTypeCode])) {
            return $this->attributeSets[$entityTypeCode];
        }

        return array();
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The processor instance
     */
    protected function getImportProcessor()
    {
        return $this->importProcessor;
    }
}
