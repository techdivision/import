<?php

/**
 * TechDivision\Import\Observers\Product\ProductBundleObserver
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

namespace TechDivision\Import\Observers\Product;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\ProductTypes;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductBundleObserver extends AbstractProductImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'bundles';

    /**
     *
     * @var array
     */
    protected $columns = array(
        'name'        => ColumnKeys::BUNDLE_VALUE_NAME,
        'type'        => ColumnKeys::BUNDLE_VALUE_TYPE,
        'required'    => ColumnKeys::BUNDLE_VALUE_REQUIRED,
        'sku'         => ColumnKeys::BUNDLE_VALUE_SKU,
        'price'       => ColumnKeys::BUNDLE_VALUE_PRICE,
        'default'     => ColumnKeys::BUNDLE_VALUE_DEFAULT,
        'default_qty' => ColumnKeys::BUNDLE_VALUE_DEFAULT_QTY,
        'price_type'  => ColumnKeys::BUNDLE_VALUE_PRICE_TYPE
    );

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($parentSku = $row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // query whether or not the product type is set
        if (!isset($headers[ColumnKeys::PRODUCT_TYPE])) {
            return $row;
        }

        // query whether or not we've found a bundle product
        if ($row[$headers[ColumnKeys::PRODUCT_TYPE]] !== ProductTypes::BUNDLE) {
            return $row;
        }

        // query whether or not, we've a bundle configuration
        if (!isset($row[$headers[ColumnKeys::BUNDLE_VALUES]])) {
            return;
        }

        // query whether or not, we've a bundle
        if ($bundleValues = $row[$headers[ColumnKeys::BUNDLE_VALUES]]) {

            // initialize the array for the product bundles
            $artefacts = array();

            // initialize the bundle with the found values
            foreach (explode('|', $bundleValues) as $bundleValue) {
                // initialize the product bundle itself
                $bundle = array(
                    ColumnKeys::BUNDLE_PARENT_SKU    => $parentSku,
                    ColumnKeys::BUNDLE_SKU_TYPE      => $row[$headers[ColumnKeys::BUNDLE_SKU_TYPE]],
                    ColumnKeys::BUNDLE_PRICE_TYPE    => $row[$headers[ColumnKeys::BUNDLE_PRICE_TYPE]],
                    ColumnKeys::BUNDLE_PRICE_VIEW    => $row[$headers[ColumnKeys::BUNDLE_PRICE_VIEW]],
                    ColumnKeys::BUNDLE_WEIGHT_TYPE   => $row[$headers[ColumnKeys::BUNDLE_WEIGHT_TYPE]],
                    ColumnKeys::BUNDLE_SHIPMENT_TYPE => $row[$headers[ColumnKeys::BUNDLE_SHIPMENT_TYPE]]
                );

                // initialize the columns
                foreach ($this->columns as $columnKey) {
                    $bundle[$columnKey] = null;
                }

                // set the values
                $values = array();
                foreach (explode(',', $bundleValue) as $values) {
                    list ($key, $value) = explode('=', $values);
                    $bundle[$this->columns[$key]] = $value;
                }

                // prepare and append the bundle data
                $artefacts[] = $bundle;
            }

            // append the bundles to the subject
            $this->addArtefacts($artefacts);
        }

        // returns the row
        return $row;
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(ProductBundleObserver::ARTEFACT_TYPE, $artefacts);
    }
}
