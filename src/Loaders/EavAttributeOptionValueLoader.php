<?php

/**
 * TechDivision\Import\Loaders\EavAttributeOptionValueLoader
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

use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for available option values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueLoader implements ResetAwareLoaderInterface
{

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\ImportProcessorInterface
     */
    protected $importProcessor;

    /**
     * The attribute option values.
     *
     * @var array
     */
    protected $eavAttributeOptionValues;

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // set the import processor instance
        $this->importProcessor = $importProcessor;

        // reset the loader instance
        $this->reset();
    }

    /**
     * Loads and returns data the custom validation data.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the validations from
     * @param \TechDivision\Import\Subjects\SubjectInterface                  $subject       The subject instance to load the validations from
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(SubjectConfigurationInterface $configuration = null, SubjectInterface $subject = null)
    {
        // load the entity type code from the passed subject configuration
        if ($subject) {
            $entityTypeCode = $subject->getEntityTypeCode();
        } else {
            $entityTypeCode = $configuration->getExecutionContext()->getEntityTypeCode();
        }
        
        // return the available attribute option values for the entity type
        if (isset($this->eavAttributeOptionValues[$entityTypeCode])) {
            return $this->eavAttributeOptionValues[$entityTypeCode];
        }

        // return an empty array otherwise
        return array();
    }

    /**
     * Reset's the loader instance.
     *
     * @return void
     */
    public function reset()
    {

        // load the entity types
        $entityTypes = $this->getImportProcessor()->getEavEntityTypes();

        // prepare the array with the attribute sets
        foreach ($entityTypes as $entityType) {
            // prepare the array with the attribute sets
            foreach ($this->getImportProcessor()->getEavAttributeOptionValuesByEntityTypeIdAndStoreId($entityType[MemberNames::ENTITY_TYPE_ID], 0) as $eavAttributeOptionValue) {
                $this->eavAttributeOptionValues[$entityType[MemberNames::ENTITY_TYPE_CODE]][$eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE]][] = $eavAttributeOptionValue[MemberNames::VALUE];
            }
        }
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
