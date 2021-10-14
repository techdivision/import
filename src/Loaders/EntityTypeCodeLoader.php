<?php

/**
 * TechDivision\Import\Loaders\EntityTypeCodeLoader
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
 * Loader for entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EntityTypeCodeLoader implements LoaderInterface
{

    /**
     * The entity type codes.
     *
     * @var array
     */
    protected $entityTypeCodes = array();

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // load the entity types
        $entityTypes = $importProcessor->getEavEntityTypes();

        // prepare the array with the entity type codes
        foreach ($entityTypes as $entityType) {
            $this->entityTypeCodes[] = $entityType[MemberNames::ENTITY_TYPE_CODE];
        }
    }

    /**
     * Loads and returns the entity type codes.
     *
     * @return \ArrayAccess The array with the entity type codes
     */
    public function load()
    {
        return $this->entityTypeCodes;
    }
}
