<?php

/**
 * TechDivision\Import\Utils\EntityTypeCodes
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class containing the available entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EntityTypeCodes extends \ArrayObject
{

    /**
     * Key for the imports without entity.
     *
     * @var integer
     */
    const NONE = 'none';

    /**
     * Key for debugging purposes.
     *
     * @var integer
     */
    const DEBUG = 'debug';

    /**
     * Key for the product entity 'catalog_product'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT = 'catalog_product';

    /**
     * Key for the product entity 'catalog_product_url'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_URL = 'catalog_product_url';

    /**
     * Key for the product entity 'catalog_product_price'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_PRICE = 'catalog_product_price';

    /**
     * Key for the product entity 'catalog_product_tier_price'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_TIER_PRICE = 'catalog_product_tier_price';

    /**
     * Key for the product entity 'catalog_product_inventory'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_INVENTORY = 'catalog_product_inventory';

    /**
     * Key for the product entity 'catalog_product_inventory_msi'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_INVENTORY_MSI = 'catalog_product_inventory_msi';

    /**
     * Key for the category entity 'catalog_category'.
     *
     * @var integer
     */
    const CATALOG_CATEGORY = 'catalog_category';

    /**
     * Key for the customer entity 'customer'.
     *
     * @var integer
     */
    const CUSTOMER = 'customer';

    /**
     * Key for the customer entity 'customer_address'.
     *
     * @var integer
     */
    const CUSTOMER_ADDRESS = 'customer_address';

    /**
     * Key for the attribute entity 'eav_attribute'.
     *
     * @var integer
     */
    const EAV_ATTRIBUTE = 'eav_attribute';

    /**
     * Key for the attribute entity 'eav_attribute_set'.
     *
     * @var integer
     */
    const EAV_ATTRIBUTE_SET = 'eav_attribute_set';

    /**
     * Key for the product entity 'catalog_product_simple'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_SIMPLE = 'catalog_product_simple';

    /**
     * Key for the product entity 'catalog_product_simple'.
     *
     * @var integer
     */
    const CATALOG_PRODUCT_BUNDLE = 'catalog_product_bundle';

    /**
     * Construct a new entity type codes instance.
     *
     * @param array $entityTypeCodes The array with the additional entity type codes
     * @link http://www.php.net/manual/en/arrayobject.construct.php
     */
    public function __construct(array $entityTypeCodes = array())
    {

        // merge the entity type codes with the passed ones
        $mergedEntityTypeCodes = array_merge(
            array(
                EntityTypeCodes::NONE,
                EntityTypeCodes::CATALOG_PRODUCT,
                EntityTypeCodes::CATALOG_PRODUCT_URL,
                EntityTypeCodes::CATALOG_PRODUCT_PRICE,
                EntityTypeCodes::CATALOG_PRODUCT_INVENTORY,
                EntityTypeCodes::CATALOG_CATEGORY,
                EntityTypeCodes::EAV_ATTRIBUTE,
                EntityTypeCodes::EAV_ATTRIBUTE_SET,
                EntityTypeCodes::CATALOG_PRODUCT_SIMPLE,
                EntityTypeCodes::CUSTOMER,
                EntityTypeCodes::CUSTOMER_ADDRESS
            ),
            $entityTypeCodes
        );

        // initialize the parent class with the merged entity type codes
        parent::__construct($mergedEntityTypeCodes);
    }
}
