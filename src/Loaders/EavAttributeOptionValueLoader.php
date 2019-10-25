<?php

/**
 * TechDivision\Import\Loaders\EavAttributeOptionValueLoader
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
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for available option values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueLoader implements LoaderInterface
{

    /**
     * The attribute option values.
     *
     * @var array
     */
    protected $eavAttributeOptionValues = array();

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
            // prepare the array with the attribute sets
            foreach ($importProcessor->getEavAttributeOptionValuesByEntityTypeIdAndStoreId($entityType[MemberNames::ENTITY_TYPE_ID], 0) as $eavAttributeOptionValue) {
                $this->eavAttributeOptionValues[$entityType[MemberNames::ENTITY_TYPE_CODE]][$eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE]][] = $eavAttributeOptionValue[MemberNames::VALUE];
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

        // load the entity type code from the passed subject configuration
        $entityTypeCode = $configuration->getExecutionContext()->getEntityTypeCode();

        // return the available attribute option values for the entity type
        if (isset($this->eavAttributeOptionValues[$entityTypeCode])) {
            return $this->eavAttributeOptionValues[$entityTypeCode];
        }

        // return an empty array otherwise
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
