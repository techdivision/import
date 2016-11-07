<?php

/**
 * TechDivision\Import\Utils\SqlStatements
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Utils;

/**
 * A SSB providing process registry functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class SqlStatements
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Return's the Magento edition/version specific utility class containing
     * the SQL statements to use.
     *
     * @param string $magentoEdition The Magento edition to use, EE or CE
     * @param string $magentoVersion The Magento version to use, e. g. 2.1.0
     *
     * @return string The fully qualified utility class name
     */
    public static function getUtilityClassName($magentoEdition, $magentoVersion)
    {

        // prepare the Magento edition/version specific utility classname
        $utilClassName = sprintf('TechDivision\Import\Utils\%s\V%s\SqlStatements', ucfirst($magentoEdition), $magentoVersion);

        // if NOT available, use the default utility class name
        if (!class_exists($utilClassName)) {
            // prepare the Magento edition/version specific utility classname
            if (!class_exists($utilClassName = sprintf('TechDivision\Import\Utils\%s\SqlStatements', ucfirst($magentoEdition)))) {
                $utilClassName = __CLASS__;
            }
        }

        // return the utility class name
        return $utilClassName;
    }

    /**
     * The SQL statement to delete all products.
     *
     * @var string
     */
    const DELETE_PRODUCTS = 'DELETE FROM catalog_product_entity WHERE entity_id > ?';

    /**
     * The SQL statement to create new products.
     *
     * @var string
     */
    const CREATE_PRODUCT = 'INSERT
                              INTO catalog_product_entity (
                                       sku,
                                       created_at,
                                       updated_at,
                                       has_options,
                                       required_options,
                                       type_id,
                                       attribute_set_id
                                   )
                            VALUES (?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product website relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_WEBSITE = 'INSERT
                                      INTO catalog_product_website (
                                               product_id,
                                               website_id
                                           )
                                    VALUES (?, ?)';

    /**
     * The SQL statement to create a new product category relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_CATEGORY = 'INSERT
                                       INTO catalog_category_product (
                                                category_id,
                                                product_id,
                                                position
                                            )
                                     VALUES (?, ?, ?)';

    /**
     * The SQL statement to create a new product datetime value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DATETIME = 'INSERT
                                       INTO catalog_product_entity_datetime (
                                                entity_id,
                                                attribute_id,
                                                store_id,
                                                value
                                            )
                                    VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product decimal value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DECIMAL = 'INSERT
                                      INTO catalog_product_entity_decimal (
                                               entity_id,
                                               attribute_id,
                                               store_id,
                                               value
                                           )
                                   VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product integer value.
     *
     * @var string
     */
    const CREATE_PRODUCT_INT = 'INSERT
                                  INTO catalog_product_entity_int (
                                           entity_id,
                                           attribute_id,
                                           store_id,
                                           value
                                       )
                                VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product varchar value.
     *
     * @var string
     */
    const CREATE_PRODUCT_VARCHAR = 'INSERT
                                      INTO catalog_product_entity_varchar (
                                               entity_id,
                                               attribute_id,
                                               store_id,
                                               value
                                           )
                                    VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product text value.
     *
     * @var string
     */
    const CREATE_PRODUCT_TEXT = 'INSERT
                                   INTO catalog_product_entity_text (
                                            entity_id,
                                            attribute_id,
                                            store_id,
                                            value
                                        )
                                 VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_STATUS = 'INSERT
                                   INTO cataloginventory_stock_status (
                                            product_id,
                                            website_id,
                                            stock_id,
                                            qty,
                                            stock_status
                                        )
                                 VALUES (?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_ITEM = 'INSERT
                                 INTO cataloginventory_stock_item (
                                          product_id,
                                          stock_id,
                                          website_id,
                                          qty,
                                          min_qty,
                                          use_config_min_qty,
                                          is_qty_decimal,
                                          backorders,
                                          use_config_backorders,
                                          min_sale_qty,
                                          use_config_min_sale_qty,
                                          max_sale_qty,
                                          use_config_max_sale_qty,
                                          is_in_stock,
                                          notify_stock_qty,
                                          use_config_notify_stock_qty,
                                          manage_stock,
                                          use_config_manage_stock,
                                          use_config_qty_increments,
                                          qty_increments,
                                          use_config_enable_qty_inc,
                                          enable_qty_increments,
                                          is_decimal_divided
                                      )
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_RELATION = 'INSERT
                                       INTO catalog_product_relation (
                                                parent_id,
                                                child_id
                                            )
                                     VALUES (?, ?)';

    /**
     * The SQL statement to create a new product super link.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_LINK = 'INSERT
                                         INTO catalog_product_super_link (
                                                  product_id,
                                                  parent_id
                                              )
                                       VALUES (?, ?)';

    /**
     * The SQL statement to create a new product super attribute.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE = 'INSERT
                                              INTO catalog_product_super_attribute (
                                                       product_id,
                                                       attribute_id,
                                                       position
                                                   )
                                            VALUES (?, ?, ?)';

    /**
     * The SQL statement to create a new product bundle option.
     *
     * @var string
     */
    const CREATE_PRODUCT_BUNDLE_OPTION = 'INSERT
                                            INTO catalog_product_bundle_option (
                                                     parent_id,
                                                     required,
                                                     position,
                                                     type
                                                   )
                                            VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product bundle option value.
     *
     * @var string
     */
    const CREATE_PRODUCT_BUNDLE_OPTION_VALUE = 'INSERT
                                                  INTO catalog_product_bundle_option_value (
                                                           option_id,
                                                           store_id,
                                                           title
                                                       )
                                                VALUES (?, ?, ?)';

    /**
     * The SQL statement to create a new product bundle selection.
     *
     * @var string
     */
    const CREATE_PRODUCT_BUNDLE_SELECTION = 'INSERT
                                               INTO catalog_product_bundle_selection (
                                                        option_id,
                                                        parent_product_id,
                                                        product_id,
                                                        position,
                                                        is_default,
                                                        selection_price_type,
                                                        selection_price_value,
                                                        selection_qty,
                                                        selection_can_change_qty
                                                    )
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product bundle selection price.
     *
     * @var string
     */
    const CREATE_PRODUCT_BUNDLE_SELECTION_PRICE = 'INSERT
                                                     INTO catalog_product_bundle_selection_price (
                                                              selection_id,
                                                              website_id,
                                                              selection_price_type,
                                                              selection_price_value
                                                          )
                                                   VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product super attribute label.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'INSERT
                                                    INTO catalog_product_super_attribute_label (
                                                             product_super_attribute_id,
                                                             store_id,
                                                             use_default,
                                                             value
                                                         )
                                                  VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to load all available categories.
     *
     * @var string
     */
    const CATEGORIES = 'SELECT t1.* FROM catalog_category_entity AS t1';

    /**
     * The SQL statement to load the category varchars for a list of entity IDs.
     *
     * @var string
     */
    const CATEGORY_VARCHARS_BY_ENTITY_IDS = 'SELECT t1.*
                                               FROM catalog_category_entity_varchar AS t1
                                         INNER JOIN eav_attribute AS t2
                                                 ON t2.entity_type_id = 3
                                                AND t2.attribute_code = \'name\'
                                                AND t1.attribute_id = t2.attribute_id
                                                AND t1.store_id = 0
                                                AND t1.entity_id IN (?)';

    /**
     * The SQL statement to load the stores.
     *
     * @var string
     */
    const STORES = 'SELECT t1.* FROM store AS t1';

    /**
     * The SQL statement to load the stores.
     *
     * @var string
     */
    const STORE_WEBSITES = 'SELECT t1.* FROM store_website AS t1';

    /**
     * The SQL statement to load the tax classes.
     *
     * @var string
     */
    const TAX_CLASSES = 'SELECT t1.* FROM tax_class AS t1';

    /**
     * The SQL statement to load the attribute set.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SET = 'SELECT t1.*
                                 FROM eav_attribute_set AS t1
                                WHERE attribute_set_id = ?';

    /**
     * The SQL statement to load the attribute sets for a specific entity type.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID = 'SELECT t1.*
                                                    FROM eav_attribute_set AS t1
                                                   WHERE entity_type_id = ?';

    /**
     * The SQL statement to load EAV attributes by entity type ID and attribute set name.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME = 'SELECT t3.*
                                                                       FROM eav_attribute AS t3
                                                                 INNER JOIN eav_entity_type AS t0
                                                                         ON t0.entity_type_id = ?
                                                                 INNER JOIN eav_attribute_set AS t1
                                                                         ON t1.attribute_set_name = ?
                                                                        AND t1.entity_type_id = t0.entity_type_id
                                                                 INNER JOIN eav_entity_attribute AS t2
                                                                         ON t2.attribute_set_id = t1.attribute_set_id
                                                                        AND t3.attribute_id = t2.attribute_id';

    /**
     * The SQL statement to load EAV attributes by attribute option value and store ID.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID = 'SELECT t1.*
                                                           FROM eav_attribute AS t1
                                                     INNER JOIN eav_attribute_option_value AS t2
                                                             ON t2.value = ?
                                                            AND t2.store_id = ?
                                                     INNER JOIN eav_attribute_option AS t3
                                                             ON t3.option_id = t2.option_id
                                                            AND t1.attribute_id = t3.attribute_id';

    /**
     * The SQL statement to load the attribute option value.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUE = 'SELECT t1.*
                                          FROM eav_attribute_option_value AS t1
                                         WHERE t1.value = ?
                                           AND t1.store_id = ?';
}