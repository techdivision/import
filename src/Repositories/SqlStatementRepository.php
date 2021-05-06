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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Repository class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatementRepository extends AbstractSqlStatementRepository
{

    /**
     * The SQL statements.
     *
     * @var array
     */
    private $statements = array(
        SqlStatementKeys::CATEGORIES =>
            'SELECT t0.*,
                    (SELECT `value`
                       FROM ${table:eav_attribute} t1, ${table:catalog_category_entity_varchar} t2
                      WHERE t1.attribute_code = \'name\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.${pk:entity_id} = t0.${pk:entity_id}) AS name,
                    (SELECT `value`
                       FROM ${table:eav_attribute} t1, ${table:catalog_category_entity_varchar} t2
                      WHERE t1.attribute_code = \'url_key\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.${pk:entity_id} = t0.${pk:entity_id}) AS url_key,
                    (SELECT `value`
                       FROM ${table:eav_attribute} t1, ${table:catalog_category_entity_varchar} t2
                      WHERE t1.attribute_code = \'url_path\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.${pk:entity_id} = t0.${pk:entity_id}) AS url_path,
                    (SELECT `value`
                       FROM ${table:eav_attribute} t1, ${table:catalog_category_entity_varchar} t2
                      WHERE t1.attribute_code = \'is_anchor\'
                        AND t1.entity_type_id = 3
                        AND t2.attribute_id = t1.attribute_id
                        AND t2.store_id = 0
                        AND t2.${pk:entity_id} = t0.${pk:entity_id}) AS is_anchor
               FROM ${table:catalog_category_entity} AS t0',
        SqlStatementKeys::ROOT_CATEGORIES =>
            'SELECT t2.code, t0.*
               FROM ${table:catalog_category_entity} t0
         INNER JOIN ${table:store_group} t1
                 ON t1.root_category_id = t0.entity_id
         INNER JOIN ${table:store} t2
                 ON t2.group_id = t1.group_id',
        SqlStatementKeys::CATEGORIES_BY_STORE_VIEW =>
            'SELECT t0.*,
                 IF (name_store.value_id > 0, name_store.value, name_default.value) AS name,
                 IF (url_key_store.value_id > 0, url_key_store.value, url_key_default.value) AS url_key,
                 IF (url_path_store.value_id > 0, url_path_store.value, url_path_default.value) AS url_path,
                 IF (is_anchor_store.value_id > 0, is_anchor_store.value, is_anchor_default.value) AS is_anchor
               FROM ${table:catalog_category_entity} AS t0
          LEFT JOIN ${table:catalog_category_entity_varchar} AS name_store
                 ON name_store.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'name\' AND entity_type_id = 3
                    )
                    AND name_store.store_id = :store_id
                    AND name_store.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_varchar} AS name_default
                 ON name_default.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'name\' AND entity_type_id = 3
                    )
                    AND name_default.store_id = 0
                    AND name_default.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_varchar} AS url_key_store
                 ON url_key_store.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'url_key\' AND entity_type_id = 3
                    )
                    AND url_key_store.store_id = :store_id
                    AND url_key_store.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_varchar} AS url_key_default
                 ON url_key_default.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'url_key\' AND entity_type_id = 3
                    )
                    AND url_key_default.store_id = 0
                    AND url_key_default.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_varchar} AS url_path_store
                 ON url_path_store.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'url_path\' AND entity_type_id = 3
                    )
                    AND url_path_store.store_id = :store_id
                    AND url_path_store.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_varchar} AS url_path_default
                 ON url_path_default.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'url_path\' AND entity_type_id = 3
                    )
                    AND url_path_default.store_id = 0
                    AND url_path_default.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_int} AS is_anchor_store
                 ON is_anchor_store.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'is_anchor\' AND entity_type_id = 3
                    )
                    AND is_anchor_store.store_id = :store_id
                    AND is_anchor_store.entity_id = t0.entity_id
          LEFT JOIN ${table:catalog_category_entity_int} AS is_anchor_default
                 ON is_anchor_default.attribute_id = (
                        SELECT attribute_id FROM ${table:eav_attribute}
                        WHERE attribute_code = \'is_anchor\' AND entity_type_id = 3
                    )
                    AND is_anchor_default.store_id = 0
                    AND is_anchor_default.entity_id = t0.entity_id',
        SqlStatementKeys::ROOT_CATEGORIES =>
            'SELECT t2.code, t0.*
               FROM ${table:catalog_category_entity} t0
         INNER JOIN ${table:store_group} t1
                 ON t1.root_category_id = t0.entity_id
         INNER JOIN ${table:store} t2
                 ON t2.group_id = t1.group_id',
        SqlStatementKeys::CATEGORY_VARCHARS_BY_ENTITY_IDS =>
            'SELECT t1.*
               FROM ${table:catalog_category_entity_varchar} AS t1
         INNER JOIN ${table:eav_attribute} AS t2
                 ON t2.entity_type_id = 3
                AND t2.attribute_code = \'name\'
                AND t1.attribute_id = t2.attribute_id
                AND t1.store_id = 0
                AND t1.entity_id IN (?)',
        SqlStatementKeys::STORES =>
            'SELECT t1.* FROM ${table:store} AS t1',
        SqlStatementKeys::STORE_DEFAULT =>
            'SELECT t0.*
               FROM ${table:store} t0
         INNER JOIN ${table:store_group} t1
                 ON t1.default_store_id = t0.store_id
         INNER JOIN ${table:store_website} t2
                 ON t1.website_id = t2.website_id
                AND t2.is_default = 1;',
        SqlStatementKeys::STORE_WEBSITES =>
            'SELECT t1.* FROM ${table:store_website} AS t1',
        SqlStatementKeys::STORE_GROUPS =>
            'SELECT t1.* FROM ${table:store_group} AS t1',
        SqlStatementKeys::TAX_CLASSES =>
            'SELECT t1.* FROM ${table:tax_class} AS t1',
        SqlStatementKeys::LINK_TYPES =>
            'SELECT t1.* FROM ${table:catalog_product_link_type} AS t1',
        SqlStatementKeys::LINK_ATTRIBUTES =>
            'SELECT t1.* FROM ${table:catalog_product_link_attribute} AS t1',
        SqlStatementKeys::EAV_ENTITY_TYPES =>
            'SELECT t1.* FROM ${table:eav_entity_type} AS t1',
        SqlStatementKeys::EAV_ENTITY_TYPE_BY_ENTITY_TYPE_CODE =>
            'SELECT *
               FROM ${table:eav_entity_type}
              WHERE entity_type_code = :entity_type_code',
        SqlStatementKeys::EAV_ATTRIBUTE =>
            'SELECT *
               FROM ${table:eav_attribute}
              WHERE attribute_id = :attribute_id',
        SqlStatementKeys::EAV_ATTRIBUTE_SET =>
            'SELECT t1.*
               FROM ${table:eav_attribute_set} AS t1
              WHERE attribute_set_id = ?',
        SqlStatementKeys::EAV_ATTRIBUTE_GROUP =>
            'SELECT t1.*
               FROM ${table:eav_attribute_group} AS t1
              WHERE attribute_group_id = :attribute_group_id',
        SqlStatementKeys::EAV_ATTRIBUTE_GROUP_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME_AND_ATTRIBUTE_GROUP_NAME =>
            'SELECT t1.*
               FROM ${table:eav_attribute_group} AS t1
         INNER JOIN ${table:eav_entity_type} t3
                 ON t3.entity_type_code = :entity_type_code
         INNER JOIN ${table:eav_attribute_set} t2
                 ON t2.attribute_set_name = :attribute_set_name
                AND t2.entity_type_id = t3.entity_type_id
                AND t1.attribute_set_id = t2.attribute_set_id
              WHERE attribute_group_name = :attribute_group_name',
        SqlStatementKeys::EAV_ATTRIBUTE_SETS_BY_ENTITY_TYPE_ID =>
            'SELECT t1.*
               FROM ${table:eav_attribute_set} AS t1
              WHERE entity_type_id = ?',
        SqlStatementKeys::EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME =>
            'SELECT t1.*
               FROM ${table:eav_attribute_set} AS t1
         INNER JOIN ${table:eav_entity_type} t2
                 ON t2.entity_type_code = :entity_type_code
                AND t1.entity_type_id = t2.entity_type_id
              WHERE t1.attribute_set_name = :attribute_set_name',
        SqlStatementKeys::EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME =>
            'SELECT *
               FROM ${table:eav_attribute_set}
              WHERE entity_type_id = :entity_type_id
                AND attribute_set_name = :attribute_set_name',
        SqlStatementKeys::EAV_ATTRIBUTE_GROUPS_BY_ATTRIBUTE_SET_ID =>
            'SELECT *
               FROM ${table:eav_attribute_group}
              WHERE attribute_set_id = :attribute_set_id',
        SqlStatementKeys::EAV_ATTRIBUTE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE =>
            'SELECT *
               FROM ${table:eav_attribute}
              WHERE entity_type_id = :entity_type_id
                AND attribute_code = :attribute_code',
        SqlStatementKeys::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME =>
            'SELECT t3.*,t4.is_global
               FROM ${table:eav_attribute} AS t3
         INNER JOIN ${table:eav_entity_type} AS t0
                 ON t0.entity_type_id = :entity_type_id
         INNER JOIN ${table:eav_attribute_set} AS t1
                 ON t1.attribute_set_name = :attribute_set_name
                AND t1.entity_type_id = t0.entity_type_id
         INNER JOIN ${table:eav_entity_attribute} AS t2
                 ON t2.attribute_set_id = t1.attribute_set_id
                AND t3.attribute_id = t2.attribute_id
         LEFT JOIN ${table:catalog_eav_attribute} AS t4 ON t3.attribute_id = t4.attribute_id',
        SqlStatementKeys::EAV_ATTRIBUTES_BY_OPTION_VALUE_AND_STORE_ID =>
            'SELECT t1.*
               FROM ${table:eav_attribute} AS t1
         INNER JOIN ${table:eav_attribute_option_value} AS t2
                 ON t2.value = BINARY :value
                AND t2.store_id = :store_id
         INNER JOIN ${table:eav_attribute_option} AS t3
                 ON t3.option_id = t2.option_id
                AND t1.attribute_id = t3.attribute_id',
        SqlStatementKeys::EAV_ATTRIBUTES_BY_IS_USER_DEFINED =>
            'SELECT * FROM ${table:eav_attribute} WHERE is_user_defined = :is_user_defined',
        SqlStatementKeys::EAV_ATTRIBUTES_BY_ENTITY_TYPE_ID_AND_IS_USER_DEFINED =>
            'SELECT *
               FROM ${table:eav_attribute}
              WHERE entity_type_id = :entity_type_id
                AND is_user_defined = :is_user_defined',
        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE =>
            'SELECT t3.*
               FROM ${table:eav_attribute} t1,
                    ${table:eav_attribute_option} t2,
                    ${table:eav_attribute_option_value} t3
              WHERE t1.attribute_code = :attribute_code
                AND t3.store_id = :store_id
                AND t3.value = BINARY :value
                AND t2.attribute_id = t1.attribute_id
                AND t2.option_id = t3.option_id',
        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE =>
            'SELECT t3.*
               FROM ${table:eav_attribute} t1,
                    ${table:eav_attribute_option} t2,
                    ${table:eav_attribute_option_value} t3
              WHERE t1.attribute_code = :attribute_code
                AND t1.entity_type_id = :entity_type_id
                AND t3.store_id = :store_id
                AND t3.value = BINARY :value
                AND t2.attribute_id = t1.attribute_id
                AND t2.option_id = t3.option_id',
        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID =>
            'SELECT t1.*
               FROM ${table:eav_attribute_option_value} t1
              WHERE t1.option_id = :option_id
                AND t1.store_id = :store_id',
        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUES =>
            'SELECT t3.*, t1.attribute_code
               FROM ${table:eav_attribute} t1,
                    ${table:eav_attribute_option} t2,
                    ${table:eav_attribute_option_value} t3
              WHERE t2.option_id = t3.option_id
                AND t1.attribute_id = t2.attribute_id',
        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUES_BY_ENTITY_TYPE_ID_AND_STORE_ID =>
            'SELECT t3.*, t1.attribute_code
               FROM ${table:eav_attribute} t1,
                    ${table:eav_attribute_option} t2,
                    ${table:eav_attribute_option_value} t3
              WHERE t2.option_id = t3.option_id
                AND t1.attribute_id = t2.attribute_id
                AND t1.entity_type_id = :entity_type_id
                AND t3.store_id = :store_id',
        SqlStatementKeys::CORE_CONFIG_DATA =>
            'SELECT * FROM ${table:core_config_data}',
        SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID =>
            'SELECT *
               FROM ${table:url_rewrite}
              WHERE entity_type = :entity_type
                AND entity_id = :entity_id',
        SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID =>
            'SELECT *
               FROM ${table:url_rewrite}
              WHERE entity_type = :entity_type
                AND entity_id = :entity_id
                AND store_id = :store_id',
        SqlStatementKeys::DELETE_URL_REWRITE =>
            'DELETE
               FROM ${table:url_rewrite}
              WHERE url_rewrite_id = :url_rewrite_id',
        SqlStatementKeys::DELETE_URL_REWRITE_BY_SKU =>
            'DELETE t1.*
               FROM ${table:url_rewrite} t1
         INNER JOIN ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.entity_id = t2.entity_id',
        SqlStatementKeys::DELETE_URL_REWRITE_BY_PATH =>
            'DELETE t1.*
               FROM ${table:url_rewrite} t1
         INNER JOIN ${table:catalog_category_entity} t2
              WHERE t2.path = :path
                AND t1.entity_id = t2.entity_id
                AND t1.entity_type = \'category\'',
        SqlStatementKeys::DELETE_URL_REWRITE_BY_CATEGORY_ID =>
            'DELETE t1.*
               FROM ${table:url_rewrite} t1
         INNER JOIN ${table:catalog_url_rewrite_product_category} t2
              WHERE t2.category_id = :category_id
                AND t1.url_rewrite_id = t2.url_rewrite_id',
        SqlStatementKeys::CREATE_URL_REWRITE =>
            'INSERT
               INTO ${table:url_rewrite}
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
        SqlStatementKeys::UPDATE_URL_REWRITE =>
            'UPDATE ${table:url_rewrite}
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
        SqlStatementKeys::CREATE_STORE =>
            'INSERT
               INTO ${table:store}
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
        SqlStatementKeys::UPDATE_STORE =>
            'UPDATE ${table:store}
                SET code = :code,
                    website_id = :website_id,
                    group_id = :group_id,
                    name = :name,
                    sort_order = :sort_order,
                    is_active = :is_active
              WHERE store_id = :store_id',
        SqlStatementKeys::CREATE_STORE_GROUP =>
            'INSERT
               INTO ${table:store_group}
                    (website_id,
                     name,
                     root_category_id,
                     default_store_id)
             VALUES (:website_id,
                     :name,
                     :root_category_id,
                     :default_store_id)',
        SqlStatementKeys::UPDATE_STORE_GROUP =>
            'UPDATE ${table:store_group}
                SET website_id = :website_id,
                    name = :name,
                    root_category_id = :root_category_id,
                    default_store_id = :default_store_id
              WHERE group_id = :group_id',
        SqlStatementKeys::CREATE_STORE_WEBSITE =>
            'INSERT
               INTO ${table:store_website}
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
        SqlStatementKeys::UPDATE_STORE_WEBSITE =>
            'UPDATE ${table:store_website}
                SET code = :code,
                    name = :name,
                    sort_order = :sort_order,
                    default_group_id = :default_group_id,
                    is_default = :is_default
              WHERE website_id = :website_id',
        SqlStatementKeys::IMAGE_TYPES =>
            'SELECT main_table.attribute_code,
                    main_table.attribute_id,
                    main_table.is_user_defined
               FROM ${table:eav_attribute} AS main_table
         INNER JOIN ${table:eav_entity_type} AS entity_type
                 ON main_table.entity_type_id = entity_type.entity_type_id
          LEFT JOIN ${table:eav_entity_attribute}
                 ON main_table.attribute_id = ${table:eav_entity_attribute}.attribute_id
         INNER JOIN ${table:catalog_eav_attribute} AS additional_table
                 ON main_table.attribute_id = additional_table.attribute_id
              WHERE (entity_type_code = \'catalog_product\')
                AND (frontend_input = \'media_image\')
           GROUP BY main_table.attribute_code,
                    main_table.attribute_id,
                    main_table.is_user_defined
           ORDER BY main_table.is_user_defined,
                    main_table.attribute_id',
        SqlStatementKeys::CUSTOMER_GROUPS =>
            'SELECT * FROM ${table:customer_group}',
        SqlStatementKeys::DELETE_IMPORT_HISTORY =>
            'DELETE
               FROM ${table:import_history}
              WHERE history_id = :history_id',
        SqlStatementKeys::UPDATE_IMPORT_HISTORY =>
            'UPDATE ${table:import_history}
                SET user_id = :user_id,
                    started_at = :started_at,
                    imported_file = :imported_file,
                    execution_time = :execution_time,
                    summary = :summary,
                    error_file = :error_file
              WHERE history_id = :history_id',
        SqlStatementKeys::CREATE_IMPORT_HISTORY =>
            'INSERT
               INTO ${table:import_history}
                    (user_id,
                     started_at,
                     imported_file,
                     execution_time,
                     summary,
                     error_file)
             VALUES (:user_id,
                     :started_at,
                     :imported_file,
                     :execution_time,
                     :summary,
                     :error_file)',
        SqlStatementKeys::ADMIN_USERS =>
            'SELECT *
               FROM ${table:admin_user}',
        SqlStatementKeys::ADMIN_USER_BY_USERNAME =>
            'SELECT *
               FROM ${table:admin_user}
              WHERE username = :username'
    );

    /**
     * Initializes the SQL statement repository with the primary key and table prefix utility.
     *
     * @param \TechDivision\Import\Utils\SqlCompilerInterface[] $compilers The array with the compiler instances
     */
    public function __construct(\IteratorAggregate $compilers)
    {

        // pass primary key + table prefix utility to parent instance
        parent::__construct($compilers);

        // compile the SQL statements
        $this->compile($this->statements);
    }
}
