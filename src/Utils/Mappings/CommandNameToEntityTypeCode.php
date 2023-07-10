<?php

/**
 * TechDivision\Import\Utils\Mappings\CommandNameToEntityTypeCode
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils\Mappings;

use TechDivision\Import\Utils\CommandNames;
use TechDivision\Import\Utils\EntityTypeCodes;

/**
 * The mapping for the command name to a entity type code.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CommandNameToEntityTypeCode extends \ArrayObject
{

    /**
     * Construct a new command name to entity type code to mapping instance.
     *
     * @param array $mappings The array with the command name to entity type code mappings
     * @link http://www.php.net/manual/en/arrayobject.construct.php
     */
    public function __construct(array $mappings = array())
    {

        // merge the entity type codes with the passed ones
        $mergedMappings = array_merge(
            array(
                CommandNames::IMPORT_PRODUCTS                  => EntityTypeCodes::CATALOG_PRODUCT,
                CommandNames::IMPORT_PRODUCTS_URL              => EntityTypeCodes::CATALOG_PRODUCT_URL,
                CommandNames::IMPORT_PRODUCTS_PRICE            => EntityTypeCodes::CATALOG_PRODUCT_PRICE,
                CommandNames::IMPORT_PRODUCTS_TIER_PRICE       => EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE,
                CommandNames::IMPORT_PRODUCTS_INVENTORY        => EntityTypeCodes::CATALOG_PRODUCT_INVENTORY,
                CommandNames::IMPORT_PRODUCTS_INVENTORY_MSI    => EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI,
                CommandNames::IMPORT_CATEGORIES                => EntityTypeCodes::CATALOG_CATEGORY,
                CommandNames::IMPORT_ATTRIBUTES                => EntityTypeCodes::EAV_ATTRIBUTE,
                CommandNames::IMPORT_ATTRIBUTES_SET            => EntityTypeCodes::EAV_ATTRIBUTE_SET,
                CommandNames::IMPORT_CUSTOMERS                 => EntityTypeCodes::CUSTOMER,
                CommandNames::IMPORT_CUSTOMERS_ADDRESS         => EntityTypeCodes::CUSTOMER_ADDRESS,
                CommandNames::IMPORT_CLEAR_PID_FILE            => EntityTypeCodes::NONE,
                CommandNames::IMPORT_CREATE_OK_FILE            => EntityTypeCodes::NONE,
                CommandNames::IMPORT_CREATE_CONFIGURATION_FILE => EntityTypeCodes::NONE,
                CommandNames::IMPORT_EXECUTE                   => EntityTypeCodes::NONE,
                CommandNames::IMPORT_CONVERT_VALUE             => EntityTypeCodes::NONE,
                CommandNames::IMPORT_DEBUG                     => EntityTypeCodes::DEBUG,
                CommandNames::IMPORT_CONFIG_DIFF               => EntityTypeCodes::NONE,
                CommandNames::IMPORT_CREATE_CONFIG             => EntityTypeCodes::NONE
            ),
            $mappings
        );

        // initialize the parent class with the merged entity type codes
        parent::__construct($mergedMappings);
    }
}
