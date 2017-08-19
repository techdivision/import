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
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatements extends AbstractSqlStatements
{

    /**
     * The SQL statement to load all available categories.
     *
     * @var string
     */
    const CATEGORIES = 'categories';

    /**
     * The SQL statement to load the root categories.
     *
     * @var string
     */
    const ROOT_CATEGORIES = 'root_categories';

    /**
     * The SQL statement to load the category varchars for a list of entity IDs.
     *
     * @var string
     */
    const CATEGORY_VARCHARS_BY_ENTITY_IDS = 'category_varchars.by.entity_ids';

    /**
     * The SQL statement to load the stores.
     *
     * @var string
     */
    const STORES = 'stores';

    /**
     * The SQL statement to load the default store.
     *
     * @var string
     */
    const STORE_DEFAULT = 'store_default';

    /**
     * The SQL statement to load the store websites.
     *
     * @var string
     */
    const STORE_WEBSITES = 'store_websites';

    /**
     * The SQL statement to load the store groups.
     *
     * @var string
     */
    const STORE_GROUPS = 'store_groups';

    /**
     * The SQL statement to load the tax classes.
     *
     * @var string
     */
    const TAX_CLASSES = 'tax_classes';

    /**
     * The SQL statement to load all available link types.
     *
     * @var string
     */
    const LINK_TYPES = 'link_types';

    /**
     * The SQL statement to load all available link types.
     *
     * @var string
     */
    const LINK_ATTRIBUTES = 'link_attributes';

    /**
     * The SQL statement to load all available EAV entity types.
     *
     * @var string
     */
    const EAV_ENTITY_TYPES = 'eav_entity_types';

    /**
     * The SQL statement to load the EAV attribute set.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SET = 'eav_attribute_set';

    /**
     * The SQL statement to load the EAV attribute group.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_GROUP = 'eav_attribute_group';

    /**
     * The SQL statement to load the attribute sets for a specific entity type.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID = 'eav_attribute_sets.by.entity_type_id';

    /**
     * The SQL statement to load the EAV attribute groups for a specific attribute set ID.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_GROUPS_BY_ATTRIBUTE_SET_ID = 'eav_attribute_groups.by.attribute_set_id';

    /**
     * The SQL statement to load EAV attributes by entity type ID and attribute set name.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME = 'eav_attributes.by.entity_type_id.and.attribute_set_name';

    /**
     * The SQL statement to load the EAV attribute option value by its attribute code, store ID and value.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID = 'eav_attribute_option_value.by.option_id.and.store_id';

    /**
     * The SQL statement to load EAV attributes by attribute option value and store ID.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID = 'eav_attributes.by.option_value.and.store_id';

    /**
     * The SQL statement to load EAV attributes by passed is user defined flag.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_IS_USER_DEFINED = 'eav_attributes.by.is_user_defined';

    /**
     * The SQL statement to load EAV attributes by passed entity type ID and is user defined flag.
     *
     * @var string
     */
    const EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_IS_USER_DEFINED = 'eav_attributes.by.entity_type_id.and.is_user_defined';

    /**
     * The SQL statement to load the EAV attribute option value by its attribute code, store ID and value.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE = 'eav_attribute_option_value.by.attribute_code.and.store_id.and.value';

    /**
     * The SQL statement to load the Magento 2 configuration.
     *
     * @var string
     */
    const CORE_CONFIG_DATA = 'core_config_data';

    /**
     * The SQL statement to load the URL rewrites for the passed entity type and ID.
     *
     * @var string
     */
    const URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID = 'url_rewrites.by.entity_type.and.entity_id';

    /**
     * The SQL statement to load the URL rewrites with the passed entity type, entity and store ID.
     *
     * @var string
     */
    const URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID = 'url_rewrites.by.entity_type.and.entity_id.and.store_id';

    /**
     * The SQL statement to remove a existing URL rewrite.
     *
     * @var string
     */
    const DELETE_URL_REWRITE = 'delete.url_rewrite';

    /**
     * The SQL statement to remove all existing URL rewrites related with the SKU passed as parameter.
     *
     * @var string
     */
    const DELETE_URL_REWRITE_BY_SKU = 'delete.url_rewrite.by.sku';

    /**
     * The SQL statement to remove all existing URL rewrites related with the category path passed as parameter.
     *
     * @var string
     */
    const DELETE_URL_REWRITE_BY_PATH = 'delete.url_rewrite.by.path';

    /**
     * The SQL statement to create new URL rewrites.
     *
     * @var string
     */
    const CREATE_URL_REWRITE = 'create.url_rewrite';

    /**
     * The SQL statement to update an existing URL rewrite.
     *
     * @var string
     */
    const UPDATE_URL_REWRITE = 'update.url_rewrite';

    /**
     * The SQL statement to create a new store.
     *
     * @var string
     */
    const CREATE_STORE = 'create.store';

    /**
     * The SQL statement to create a new store group.
     *
     * @var string
     */
    const CREATE_STORE_GROUP = 'create.store_group';

    /**
     * The SQL statement to create a new store website.
     *
     * @var string
     */
    const CREATE_STORE_WEBSITE = 'create.store_website';

    /**
     * The SQL statement to update an existing store.
     *
     * @var string
     */
    const UPDATE_STORE = 'update.store';

    /**
     * The SQL statement to update an existing store group.
     *
     * @var string
     */
    const UPDATE_STORE_GROUP = 'update.store_group';

    /**
     * The SQL statement to update an existing store website.
     *
     * @var string
     */
    const UPDATE_STORE_WEBSITE = 'update.store_website';

    /**
     * The SQL statements.
     *
     * @var array
     */
    private $statements = array(
        SqlStatements::CATEGORIES =>
            'SELECT t0.*,
                    (SELECT `value`
                       FROM eav_attribute t1, catalog_category_entity_varchar t2
                      WHERE t1.attribute_code = \'name\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.entity_id = t0.entity_id) AS name,
                    (SELECT `value`
                       FROM eav_attribute t1, catalog_category_entity_varchar t2
                      WHERE t1.attribute_code = \'url_key\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.entity_id = t0.entity_id) AS url_key,
                    (SELECT `value`
                       FROM eav_attribute t1, catalog_category_entity_varchar t2
                      WHERE t1.attribute_code = \'url_path\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.entity_id = t0.entity_id) AS url_path,
                    (SELECT `value`
                       FROM eav_attribute t1, catalog_category_entity_int t2
                      WHERE t1.attribute_code = \'is_anchor\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.entity_id = t0.entity_id) AS is_anchor
               FROM catalog_category_entity AS t0',
        SqlStatements::ROOT_CATEGORIES =>
            'SELECT t2.code, t0.*
               FROM catalog_category_entity t0
         INNER JOIN store_group t1
                 ON t1.root_category_id = t0.entity_id
         INNER JOIN store t2
                 ON t2.group_id = t1.group_id',
        SqlStatements::ROOT_CATEGORIES =>
            'SELECT t2.code, t0.*
               FROM catalog_category_entity t0
         INNER JOIN store_group t1
                 ON t1.root_category_id = t0.entity_id
         INNER JOIN store t2
                 ON t2.group_id = t1.group_id',
        SqlStatements::CATEGORY_VARCHARS_BY_ENTITY_IDS =>
            'SELECT t1.*
               FROM catalog_category_entity_varchar AS t1
         INNER JOIN eav_attribute AS t2
                 ON t2.entity_type_id = 3
                AND t2.attribute_code = \'name\'
                AND t1.attribute_id = t2.attribute_id
                AND t1.store_id = 0
                AND t1.entity_id IN (?)',
        SqlStatements::STORES =>
            'SELECT t1.* FROM store AS t1',
        SqlStatements::STORE_DEFAULT =>
            'SELECT t0.*
               FROM store t0
         INNER JOIN store_group t1
                 ON t1.default_store_id = t0.store_id
         INNER JOIN store_website t2
                 ON t1.website_id = t2.website_id
                AND t2.is_default = 1;',
        SqlStatements::STORE_WEBSITES =>
            'SELECT t1.* FROM store_website AS t1',
        SqlStatements::STORE_GROUPS =>
            'SELECT t1.* FROM store_group AS t1',
        SqlStatements::TAX_CLASSES =>
            'SELECT t1.* FROM tax_class AS t1',
        SqlStatements::LINK_TYPES =>
            'SELECT t1.* FROM catalog_product_link_type AS t1',
        SqlStatements::LINK_ATTRIBUTES =>
            'SELECT t1.* FROM catalog_product_link_attribute AS t1',
        SqlStatements::EAV_ENTITY_TYPES =>
            'SELECT t1.* FROM eav_entity_type AS t1',
        SqlStatements::EAV_ATTRIBUTE_SET =>
            'SELECT t1.*
               FROM eav_attribute_set AS t1
              WHERE attribute_set_id = ?',
        SqlStatements::EAV_ATTRIBUTE_GROUP =>
            'SELECT t1.*
               FROM eav_attribute_group AS t1
              WHERE attribute_group_id = :attribute_group_id',
        SqlStatements::EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID =>
            'SELECT t1.*
               FROM eav_attribute_set AS t1
              WHERE entity_type_id = ?',
        SqlStatements::EAV_ATTRIBUTE_GROUPS_BY_ATTRIBUTE_SET_ID =>
            'SELECT *
               FROM eav_attribute_group
              WHERE attribute_set_id = :attribute_set_id',
        SqlStatements::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME =>
            'SELECT t3.*
               FROM eav_attribute AS t3
         INNER JOIN eav_entity_type AS t0
                 ON t0.entity_type_id = :entity_type_id
         INNER JOIN eav_attribute_set AS t1
                 ON t1.attribute_set_name = :attribute_set_name
                AND t1.entity_type_id = t0.entity_type_id
         INNER JOIN eav_entity_attribute AS t2
                 ON t2.attribute_set_id = t1.attribute_set_id
                AND t3.attribute_id = t2.attribute_id',
        SqlStatements::EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID =>
            'SELECT t1.*
               FROM eav_attribute AS t1
         INNER JOIN eav_attribute_option_value AS t2
                 ON t2.value = :value
                AND t2.store_id = :store_id
         INNER JOIN eav_attribute_option AS t3
                 ON t3.option_id = t2.option_id
                AND t1.attribute_id = t3.attribute_id',
        SqlStatements::EAV_ATTRIBUTES_BY_IS_USER_DEFINED =>
            'SELECT * FROM eav_attribute WHERE is_user_defined = :is_user_defined',
        SqlStatements::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_IS_USER_DEFINED =>
            'SELECT *
               FROM eav_attribute
              WHERE entity_type_id = :entity_type_id
                AND is_user_defined = :is_user_defined',
        SqlStatements::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE =>
            'SELECT t3.*
               FROM eav_attribute t1,
                    eav_attribute_option t2,
                    eav_attribute_option_value t3
              WHERE t1.attribute_code = :attribute_code
                AND t3.store_id = :store_id
                AND t3.value = :value
                AND t2.attribute_id = t1.attribute_id
                AND t2.option_id = t3.option_id',
        SqlStatements::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID =>
            'SELECT t1.*
               FROM eav_attribute_option_value t1
              WHERE t1.option_id = :option_id
                AND t1.store_id = :store_id',
        SqlStatements::CORE_CONFIG_DATA =>
            'SELECT * FROM core_config_data',
        SqlStatements::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID =>
            'SELECT *
               FROM url_rewrite
              WHERE entity_type = :entity_type
                AND entity_id = :entity_id',
        SqlStatements::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID =>
            'SELECT *
               FROM url_rewrite
              WHERE entity_type = :entity_type
                AND entity_id = :entity_id
                AND store_id = :store_id',
        SqlStatements::DELETE_URL_REWRITE =>
            'DELETE
               FROM url_rewrite
              WHERE url_rewrite_id = :url_rewrite_id',
        SqlStatements::DELETE_URL_REWRITE_BY_SKU =>
            'DELETE url_rewrite
               FROM url_rewrite
         INNER JOIN catalog_product_entity
              WHERE catalog_product_entity.sku = :sku
                AND url_rewrite.entity_id = catalog_product_entity.entity_id',
        SqlStatements::DELETE_URL_REWRITE_BY_PATH =>
            'DELETE url_rewrite
               FROM url_rewrite
         INNER JOIN catalog_category_entity
              WHERE catalog_category_entity.path = :path
                AND url_rewrite.entity_id = catalog_category_entity.entity_id
                AND url_rewrite.entity_type = \'category\'',
        SqlStatements::CREATE_URL_REWRITE =>
            'INSERT
               INTO url_rewrite
                    (entity_type,
                     entity_id,
                     request_path,
                     target_path,
                     redirect_type,
                     store_id,
                     description,
                     is_autogenerated,
                     metadata)
             VALUES (:entity_type,
                     :entity_id,
                     :request_path,
                     :target_path,
                     :redirect_type,
                     :store_id,
                     :description,
                     :is_autogenerated,
                     :metadata)',
        SqlStatements::UPDATE_URL_REWRITE =>
            'UPDATE url_rewrite
                SET entity_type = :entity_type,
                    entity_id = :entity_id,
                    request_path = :request_path,
                    target_path = :target_path,
                    redirect_type = :redirect_type,
                    store_id = :store_id,
                    description = :description,
                    is_autogenerated = :is_autogenerated,
                    metadata = :metadata
              WHERE url_rewrite_id = :url_rewrite_id',
        SqlStatements::CREATE_STORE =>
            'INSERT
               INTO store
                    (code,
                     website_id,
                     group_id,
                     name,
                     sort_order,
                     is_active)
             VALUES (:code,
                     :website_id,
                     :group_id,
                     :name,
                     :sort_order,
                     :is_active)',
        SqlStatements::UPDATE_STORE =>
            'UPDATE store
                SET code = :code,
                    website_id = :website_id,
                    group_id = :group_id,
                    name = :name,
                    sort_order = :sort_order,
                    is_active = :is_active
              WHERE store_id = :store_id',
        SqlStatements::CREATE_STORE_GROUP =>
            'INSERT
               INTO store_group
                    (website_id,
                     name,
                     root_category_id,
                     default_store_id)
             VALUES (:website_id,
                     :name,
                     :root_category_id,
                     :default_store_id)',
        SqlStatements::UPDATE_STORE_GROUP =>
            'UPDATE store_group
                SET website_id = :website_id,
                    name = :name,
                    root_category_id = :root_category_id,
                    default_store_id = :default_store_id
              WHERE group_id = :group_id',
        SqlStatements::CREATE_STORE_WEBSITE =>
            'INSERT
               INTO store_website
                    (code,
                     name,
                     sort_order,
                     default_group_id,
                     is_default)
             VALUES (:code,
                     :name,
                     :sort_order,
                     :default_group_id,
                     :is_default)',
        SqlStatements::UPDATE_STORE_WEBSITE =>
            'UPDATE store_website
                SET code = :code,
                    name = :name,
                    sort_order = :sort_order,
                    default_group_id = :default_group_id,
                    is_default = :is_default
              WHERE website_id = :website_id',
    );

    /**
     * Initialize the the SQL statements.
     */
    public function __construct()
    {

        // merge the class statements
        foreach ($this->statements as $key => $statement) {
            $this->preparedStatements[$key] = $statement;
        }
    }
}
