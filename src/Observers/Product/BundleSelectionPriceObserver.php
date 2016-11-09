<?php

/**
 * TechDivision\Import\Observers\Product\BundleSelectionPriceObserver
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
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\StoreViewCodes;
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
class BundleSelectionPriceObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // initialize the store view code
        $storeViewCode = $row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::ADMIN;

        // load the store/website ID
        $store = $this->getStoreByStoreCode($storeViewCode);
        $websiteId = $store[MemberNames::WEBSITE_ID];

        // load the default values
        $selectionPriceType = $this->mapPriceType($row[$headers[ColumnKeys::BUNDLE_VALUE_PRICE_TYPE]]);
        $selectionPriceValue = $row[$headers[ColumnKeys::BUNDLE_VALUE_PRICE]];

        // load the selection ID for the child SKU
        $selectionId = $this->getChildSkuSelectionMapping($row[$headers[ColumnKeys::BUNDLE_VALUE_SKU]]);

        // check if we're in default store
        if (!$this->isDefaultStore($storeViewCode)) {
            // prepare the website dependent bundle selection price
            $productBundleSelectionPrice = array(
                $selectionId,
                $websiteId,
                $selectionPriceType,
                $selectionPriceValue
            );

            // persist the bundle selection price
            $this->persistProductBundleSelectionPrice($productBundleSelectionPrice);
        }

        // returns the row
        return $row;
    }

    /**
     * Return's the mapping for the passed price type.
     *
     * @param string $priceType The price type to map
     *
     * @return integer The mapped price type
     * @throws \Exception Is thrown, if the passed price type can't be mapped
     */
    public function mapPriceType($priceType)
    {
        return $this->getSubject()->mapPriceType($priceType);
    }

    /**
     * Return's the selection ID for the passed child SKU.
     *
     * @param string $childSku The child SKU to return the selection ID for
     *
     * @return integer The last created selection ID
     */
    public function getChildSkuSelectionMapping($childSku)
    {
        return $this->getSubject()->getChildSkuSelectionMapping($childSku);
    }

    /**
     * Query whether or not the passed store view code is the default one.
     *
     * @param string $storeViewCode The store view code to be queried
     *
     * @return boolean TRUE if the passed store view code is the default one, else FALSE
     */
    public function isDefaultStore($storeViewCode)
    {
        return StoreViewCodes::ADMIN === strtolower($storeViewCode);
    }

    /**
     * Return's the store for the passed store code.
     *
     * @param string $storeCode The store code to return the store for
     *
     * @return array The requested store
     * @throws \Exception Is thrown, if the requested store is not available
     */
    public function getStoreByStoreCode($storeCode)
    {
        return $this->getSubject()->getStoreByStoreCode($storeCode);
    }

    /**
     * Persist's the passed product bundle selection price data and return's the ID.
     *
     * @param array $productBundleSelectionPrice The product bundle selection price data to persist
     *
     * @return void
     */
    public function persistProductBundleSelectionPrice($productBundleSelectionPrice)
    {
        $this->getSubject()->persistProductBundleSelectionPrice($productBundleSelectionPrice);
    }
}
