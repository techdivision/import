<?php

/**
 * TechDivision\Import\Observers\Product\BundleSelectionObserver
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
class BundleSelectionObserver extends AbstractProductImportObserver
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

        if ($storeViewCode !== StoreViewCodes::ADMIN) {
            return $row;
        }

        // load the product bundle option name/SKU
        $name = $row[$headers[ColumnKeys::BUNDLE_VALUE_NAME]];
        $parentSku = $row[$headers[ColumnKeys::BUNDLE_PARENT_SKU]];

        // load parent/option ID
        $parentId = $this->mapSkuToEntityId($parentSku);

        // load the actual option ID
        $optionId = $this->getLastOptionId();

        // load the child ID
        $childSku = $row[$headers[ColumnKeys::BUNDLE_VALUE_SKU]];
        $childId = $this->mapSkuToEntityId($childSku);

        // load the default values
        $selectionCanChangeQty = 1;
        $selectionPriceType = $this->mapPriceType($row[$headers[ColumnKeys::BUNDLE_VALUE_PRICE_TYPE]]);
        $selectionPriceValue = $row[$headers[ColumnKeys::BUNDLE_VALUE_PRICE]];
        $selectionQty = $row[$headers[ColumnKeys::BUNDLE_VALUE_DEFAULT_QTY]];
        $isDefault = $row[$headers[ColumnKeys::BUNDLE_VALUE_DEFAULT]];

        // laod the position counter
        $position = $this->raisePositionCounter();

        // prepare the product bundle selection data
        $productBundleSelection = array(
            $optionId,
            $parentId,
            $childId,
            $position,
            $isDefault,
            $selectionPriceType,
            $selectionPriceValue,
            $selectionQty,
            $selectionCanChangeQty
        );

        // persist the product bundle selection data
        $this->addChildSkuSelectionIdMapping($childSku, $this->persistProductBundleSelection($productBundleSelection));

        // returns the row
        return $row;
    }

    /**
     * Return's the last created option ID.
     *
     * @return integer $optionId The last created option ID
     */
    public function getLastOptionId()
    {
        return $this->getSubject()->getLastOptionId();
    }

    /**
     * Save's the mapping of the child SKU and the selection ID.
     *
     * @param string  $childSku    The child SKU of the selection
     * @param integer $selectionId The selection ID to save
     *
     * @return void
     */
    public function addChildSkuSelectionIdMapping($childSku, $selectionId)
    {
        $this->getSubject()->addChildSkuSelectionIdMapping($childSku, $selectionId);
    }

    /**
     * Returns the acutal value of the position counter and raise's it by one.
     *
     * @return integer The actual value of the position counter
     */
    public function raisePositionCounter()
    {
        return $this->getSubject()->raisePositionCounter();
    }

    /**
     * Return's the option ID for the passed name.
     *
     * @param string $name The name to return the option ID for
     *
     * @return integer The option ID for the passed name
     * @throws \Exception Is thrown, if no option ID for the passed name is available
     */
    public function getOptionIdForName($name)
    {
        return $this->getSubject()-> getOptionIdForName($name);
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
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    public function mapSkuToEntityId($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Persist's the passed product bundle selection data and return's the ID.
     *
     * @param array $productBundleSelection The product bundle selection data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProductBundleSelection($productBundleSelection)
    {
        return $this->getSubject()->persistProductBundleSelection($productBundleSelection);
    }
}
