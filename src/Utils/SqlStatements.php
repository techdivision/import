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
