<?php

/**
 * TechDivision\Import\Utils\RegistryKeys
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
 * Utility class containing the unique registry keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RegistryKeys
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
     * Key for the registry entry containing the import status.
     *
     * @var string
     */
    const STATUS = 'status';

    /**
     * Key for the registry entry containing the import files.
     *
     * @var string
     */
    const FILES = 'files';

    /**
     * Key for the registry entry containing the number of imported bunches.
     *
     * @var string
     */
    const BUNCHES = 'bunches';

    /**
     * Key for the source directory of the next subject.
     *
     * @var string
     */
    const SOURCE_DIRECTORY = 'sourceDirectory';

    /**
     * Key for the registry entry containing the global data.
     *
     * @var string
     */
    const GLOBAL_DATA = 'globalData';

    /**
     * Key for the registry entry containing the SKU => entity ID mapping.
     *
     * @var string
     */
    const SKU_ENTITY_ID_MAPPING = 'skuEntityIdMapping';

    /**
     * Key for the registry entry containing the SKU => row ID mapping.
     *
     * @var string
     */
    const SKU_ROW_ID_MAPPING = 'skuRowIdMapping';

    /**
     * Key for the registry entry containing the SKU => store view code mapping.
     *
     * @var string
     */
    const SKU_STORE_VIEW_CODE_MAPPING = 'skuStoreViewCodeMapping';

    /**
     * Key for the registry entry containing the attribute sets.
     *
     * @var string
     */
    const ATTRIBUTE_SETS = 'attributeSets';

    /**
     * Key for the registry entry containing the eav attributes.
     *
     * @var string
     */
    const EAV_ATTRIBUTES = 'eavAttributes';

    /**
     * Key for the registry entry containing the stores.
     *
     * @var string
     */
    const STORES = 'stores';

    /**
     * Key for the registry entry containing the default store.
     *
     * @var string
     */
    const DEFAULT_STORE = 'defaultStore';

    /**
     * Key for the registry entry containing the store websites.
     *
     * @var string
     */
    const STORE_WEBSITES = 'storeWebsites';

    /**
     * Key for the registry entry containing the tax classes.
     *
     * @var string
     */
    const TAX_CLASSES = 'taxClasses';

    /**
     * Key for the registry entry containing the categories.
     *
     * @var string
     */
    const CATEGORIES = 'categories';

    /**
     * Key for the registry entry containing the root categories.
     *
     * @var string
     */
    const ROOT_CATEGORIES = 'rootCategories';

    /**
     * Key for the registry entry containing the link types.
     *
     * @var string
     */
    const LINK_TYPES = 'linkTypes';

    /**
     * Key for the registry entry containing the link attributes.
     *
     * @var string
     */
    const LINK_ATTRIBUTES = 'linkAttributes';

    /**
     * Key for the registry entry containing the Magento 2 configuration.
     *
     * @var string
     */
    const CORE_CONFIG_DATA = 'coreConfigData';
}
