<?php

/**
 * TechDivision\Import\Utils\RegistryKeys
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
 * Utility class containing the unique registry keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * Key for the target directory of the actual subject.
     *
     * @var string
     */
    const TARGET_DIRECTORY = 'targetDirectory';

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
     * Key for the registry entry containing the attribute groups.
     *
     * @var string
     */
    const ATTRIBUTE_GROUPS = 'attributeGroups';

    /**
     * Key for the registry entry containing the counters.
     *
     * @var string
     */
    const COUNTERS = 'counters';

    /**
     * Key for the registry entry containing the eav attributes.
     *
     * @var string
     */
    const EAV_ATTRIBUTES = 'eavAttributes';

    /**
     * Key for the registry entry containing the eav user defined attributes.
     *
     * @var string
     */
    const EAV_USER_DEFINED_ATTRIBUTES = 'eavUserDefinedAttributes';

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
     * Key for the registry entry containing the image types.
     *
     *
     * @var string
     */
    const IMAGE_TYPES = 'imageTypes';

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

    /**
     * Key for the registry entry containing the EAV entity types.
     *
     * @var string
     */
    const ENTITY_TYPES = 'entityTypes';

    /**
     * Key for the registry entry containing the paths => row ID mapping.
     *
     * @var string
     */
    const PATH_ROW_ID_MAPPING = 'pathRowIdMapping';

    /**
     * Key for the registry entry containing the missing attribute option values.
     *
     * @var string
     */
    const MISSING_OPTION_VALUES = 'missingOptionValues';

    /**
     * Key for the registry entry containing the customer groups.
     *
     * @var string
     */
    const CUSTOMER_GROUPS = 'customerGroups';

    /**
     * Key for the registry entry containing the name of the archive file.
     *
     * @var string
     */
    const ARCHIVE_FILE = 'archiveFile';

    /**
     * Key for the registry entry with the UNIX timestamp the import process starts.
     *
     * @var string
     */
    const STARTED_AT = 'started_at';

    /**
     * Key for the registry entry with the UNIX timestamp the import process has been finished.
     *
     * @var string
     */
    const FINISHED_AT = 'finished_at';

    /**
     * Key for the registry entry with the number of processed rows.
     *
     * @var string
     */
    const PROCESSED_ROWS = 'processed_rows';

    /**
     * Key for the registry entry with the error message.
     *
     * @var string
     */
    const ERROR_MESSAGE = 'error_message';

    /**
     * Key for the registry entry with the validation errors.
     *
     * @var string
     */
    const VALIDATIONS = 'validations';

    /**
     * Name for the strict validation cache key
     *
     * @var string
     */
    const STRICT_VALIDATIONS = 'strict-validations';

    /**
     * Key for the registry entry with collected column values.
     *
     * @var string
     */
    const COLLECTED_COLUMNS = 'collected_columns';

    /**
     * Key for the registry entry with the number of skipped rows.
     *
     * @var string
     */
    const SKIPPED_ROWS = 'skipped_rows';

    /**
     * Key for the registry entry with the serial that has to be debugged.
     *
     * @var string
     */
    const DEBUG_SERIAL = 'debug_serial';

    /**
     * Key for the registry entry with the available URL rewrites.
     *
     * @var string
     */
    const URL_REWRITES = 'url_rewrites';
}
