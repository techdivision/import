<?php

/**
 * TechDivision\Import\Utils\SqlStatementKeys
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
 * Utility class with keys of the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatementKeys
{

    /**
     * The SQL statement to load all available categories.
     *
     * @var string
     */
    const CATEGORIES = 'categories';
    /**
     * The SQL statement to load all categories by store view id.
     *
     * @var string
     */
    const CATEGORIES_BY_STORE_VIEW = 'categories_by_store_view';

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
     * The SQL statement to load the available image types by their entity type code and frontend input.
     *
     * @var string
     */
    const IMAGE_TYPES = 'image_types';

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
     * The SQL statement to load the EAV attribute option value by its entity type ID, attribute code, store ID and value.
     *
     * @var string
     */
    const EAV_ENTITY_TYPE_BY_ENTITY_TYPE_CODE = 'eav_entity_type.by.entity_type_code';

    /**
     * The SQL statement to load a EAV attribute.
     *
     * @var string
     */
    const EAV_ATTRIBUTE = 'eav_attribute';

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
     * The SQL statement to load EAV attribute group with the passed entity type code, attribute set and attribute group name.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_GROUP_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME_AND_ATTRIBUTE_GROUP_NAME = 'eav_attribute_group.by.entity_type_code.and.attribute_set_name.and.attribute_group_name';

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
     * The SQL statement to load EAV attribute by its entity type ID and attribute code.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE = 'eav_attribute.by.entity_type_id.and.attribute_code';

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
     * The SQL statement to load the available EAV attribute option values.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUES = 'eav_attribute_option_values';

    /**
     * The SQL statement to load the available EAV attribute option values by the its entity type and store ID.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUES_BY_ENTITY_TYPE_ID_AND_STORE_ID = 'eav_attribute_option_values.by.entity_type_id.and.store_id';

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
     * The SQL statement to load the EAV attribute option value by its entity type ID, attribute code, store ID and value.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE = 'eav_attribute_option_value.by.entity_type_id.and.attribute_code.and.store_id.and.value';

    /**
     * The SQL statement to load the EAV attribute set by the given entity type ID and attribute set name.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_SET_NAME = 'eav_attribute_set.by.entity_type_id.and.attribute_set_name';

    /**
     * The SQL statement to load the EAV attribute set by the given entity type code and attribute set name.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_SET_BY_ENTITY_TYPE_CODE_AND_ATTRIBUTE_SET_NAME = 'eav_attribute_set.by.entity_type_code.and.attribute_set_name';

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
     * The SQL statement to load all of the URL rewrites
     *
     * @var string
     */
    const URL_REWRITES = 'url_rewrites';

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
     * The SQL statement to remove existing product URL rewrites by their category ID.
     *
     * @var string
     */
    const DELETE_URL_REWRITE_BY_CATEGORY_ID = 'delete.url_rewrite.by.category_id';

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
     * The SQL statement to load all customer groups.
     *
     * @var string
     */
    const CUSTOMER_GROUPS = 'customer_groups';

    /**
     * The SQL statement to create a new import history entry.
     *
     * @var string
     */
    const CREATE_IMPORT_HISTORY = 'create.import_history';

    /**
     * The SQL statement to update an existing import history entry.
     *
     * @var string
     */
    const UPDATE_IMPORT_HISTORY = 'update.import_history';

    /**
     * The SQL statement to delete an existing import history entry.
     *
     * @var string
     */
    const DELETE_IMPORT_HISTORY = 'delete.import_history';

    /**
     * The SQL statement to load the available admin users.
     *
     * @var string
     */
    const ADMIN_USERS = 'admin_users';

    /**
     * The SQL statement to load the admin user with given username.
     *
     * @var string
     */
    const ADMIN_USER_BY_USERNAME = 'admin_user.by.username';
}
