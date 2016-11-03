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
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
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
            // prepare and append the bundle data
            $this->addBundle(
                array(
                    'status'  => 0,                                                  // status
                    'uid'     => $this->getUid(),                                    // UID
                    ColumnKeys::BUNDLE_VALUES        => explode('|', $bundleValues), // bundles,
                    ColumnKeys::BUNDLE_SKU_TYPE      => $row[$headers[ColumnKeys::BUNDLE_SKU_TYPE]],
                    ColumnKeys::BUNDLE_PRICE_TYPE    => $row[$headers[ColumnKeys::BUNDLE_PRICE_TYPE]],
                    ColumnKeys::BUNDLE_PRICE_VIEW    => $row[$headers[ColumnKeys::BUNDLE_PRICE_VIEW]],
                    ColumnKeys::BUNDLE_WEIGHT_TYPE   => $row[$headers[ColumnKeys::BUNDLE_WEIGHT_TYPE]],
                    ColumnKeys::BUNDLE_SHIPMENT_TYPE => $row[$headers[ColumnKeys::BUNDLE_SHIPMENT_TYPE]]
                )
            );
        }

        // returns the row
        return $row;
    }

    /**
     * Return's the UID of the file to be imported.
     *
     * @return string The UID of the file to be importded
     */
    public function getUid()
    {
        return $this->getSubject()->getUid();
    }

    /**
     * Add the passed bundle to the product with the
     * last entity ID.
     *
     * @param array $bundle The product bundle
     *
     * @return void
     * @uses \TechDivision\Import\Subjects\BunchSubject::getLastEntityId()
     */
    public function addBundle(array $bundle)
    {
        $this->getSubject()->addBundle($bundle);
    }
}
