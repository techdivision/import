<?php

/**
 * TechDivision\Import\Utils\Mappings\EntityTypeCodeMapper
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils\Mappings;

use TechDivision\Import\Utils\EntityTypeCodes;

/**
 * Mapper implementation for entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EntityTypeCodeMapper extends \ArrayObject implements MapperInterface
{

    /**
     * Construct a new command name to entity type code to mapping instance.
     *
     * @param array $mappings The array with the command name to entity type code mappings
     * @link http://www.php.net/manual/en/arrayobject.construct.php
     */
    public function __construct(array $mappings = array())
    {

        // merge the entity type code mappings with the passed ones
        $mergedMappings = array_merge(
            array(
                EntityTypeCodes::EAV_ATTRIBUTE                 => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::EAV_ATTRIBUTE_SET             => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_URL           => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_PRICE         => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => EntityTypeCodes::CATALOG_PRODUCT
            ),
            $mappings
        );

        // initialize the parent class with the merged entity type code mappings
        parent::__construct($mergedMappings);
    }

    /**
     * Map the passed entity type code.
     *
     * @param string $value The entity type code to map
     *
     * @return string The mapped entity type code
     */
    public function map(string $value) : string
    {
        return isset($this[$value]) ? $this[$value] : $value;
    }
}
