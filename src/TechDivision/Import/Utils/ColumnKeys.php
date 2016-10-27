<?php

/**
 * TechDivision\Import\Utils\ColumnKeys
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
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ColumnKeys
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
     * Name for the column 'sku'.
     *
     * @var string
     */
    const SKU = 'sku';

    /**
     * Name for the column 'categories'.
     *
     * @var string
     */
    const CATEGORIES = 'categories';

    /**
     * Name for the column 'product_type'.
     *
     * @var string
     */
    const PRODUCT_TYPE = 'product_type';

    /**
     * Name for the column 'product_websites'.
     *
     * @var string
     */
    const PRODUCT_WEBSITES = 'product_websites';

    /**
     * Name for the column 'store_view_code'.
     *
     * @var string
     */
    const STORE_VIEW_CODE = 'store_view_code';

    /**
     * Name for the column 'created_at'.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Name for the column 'updated_at'.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Name for the column 'qty'.
     *
     * @var string
     */
    const QTY = 'qty';

    /**
     * Name for the column 'is_in_stock'.
     *
     * @var string
     */
    const IS_IN_STOCK = 'is_in_stock';

    /**
     * Name for the column 'quantity_and_stock_status'.
     *
     * @var string
     */
    const QUANTITY_AND_STOCK_STATUS = 'quantity_and_stock_status';

    /**
     * Name for the column 'website_id'.
     *
     * @var string
     */
    const WEBSITE_ID = 'website_id';

    /**
     * Name for the column 'configurable_variations'.
     *
     * @var string
     */
    const CONFIGURABLE_VARIATIONS = 'configurable_variations';

    /**
     * Name for the column 'configurable_variation_labels'.
     *
     * @var string
     */
    const CONFIGURABLE_VARIATION_LABELS = 'configurable_variation_labels';

    /**
     * Name for the column 'additional_attributes'.
     *
     * @var string
     */
    const ADDITIONAL_ATTRIBUTES = 'additional_attributes';

    /**
     * Name for the column 'attribute_set_code'.
     *
     * @var string
     */
    const ATTRIBUTE_SET_CODE = 'attribute_set_code';
}
