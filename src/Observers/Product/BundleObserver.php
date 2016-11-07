<?php

/**
 * TechDivision\Import\Observers\Product\BundleObserver
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
class BundleObserver extends AbstractProductImportObserver
{

    /**
     * The value for the price type 'fixed'.
     *
     * @var integer
     */
    const PRICE_TYPE_FIXED = 0;

    /**
     * The value for the price type 'percent'.
     *
     * @var integer
     */
    const PRICE_TYPE_PERCENT = 1;

    /**
     * The position counter, if no position for the bundle selection has been specified.
     *
     * @var integer
     */
    protected $positionCounter = 1;

    /**
     * The option name => option ID mapping.
     *
     * @var array
     */
    protected $nameOptionIdMapping = array();

    /**
     * The mapping for the price type.
     *
     * @var array
     */
    protected $priceTypeMapping = array(
        'fixed'   => BundleObserver::PRICE_TYPE_FIXED,
        'percent' => BundleObserver::PRICE_TYPE_PERCENT
    );

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // load the product bundle option name/SKU
        $name = $row[$headers[ColumnKeys::BUNDLE_VALUE_NAME]];
        $parentSku = $row[$headers[ColumnKeys::BUNDLE_PARENT_SKU]];

        // load parent/option ID
        $parentId = $this->mapSkuToEntityId($parentSku);

        // initialize the store view code
        $storeViewCode = $row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::ADMIN;

        // load the store/website ID
        $store = $this->getStoreByStoreCode($storeViewCode);
        $storeId = $store[MemberNames::STORE_ID];
        $websiteId = $store[MemberNames::WEBSITE_ID];

        // query whether or not the option has already been created
        if (!$this->exists($name)) {
            // reset the position counter for the bundle selection
            $this->resetPositionCounter();

            // extract the parent/child ID as well as type and position
            $required = $row[$headers[ColumnKeys::BUNDLE_VALUE_REQUIRED]];
            $type = $row[$headers[ColumnKeys::BUNDLE_VALUE_TYPE]];
            $position = 1;

            // persist the product bundle option
            $optionId = $this->persistProductBundleOption(array($parentId, $required, $position, $type));

            // store the name => option ID mapping
            $this->addNameOptionIdMapping($name, $optionId);

            // prepare the product bundle option value
            $params = array($optionId, $storeId, $name);
            // save the product bundle option value
            $this->persistProductBundleOptionValue($params);
        }

        // load the actual option ID
        $optionId = $this->getOptionIdForName($name);

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
        $selectionId = $this->persistProductBundleSelection($productBundleSelection);

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
     * Reset the position counter to 1.
     *
     * @return void
     */
    public function resetPositionCounter()
    {
        $this->positionCounter = 1;
    }

    /**
     * Returns the acutal value of the position counter and raise's it by one.
     *
     * @return integer The actual value of the position counter
     */
    public function raisePositionCounter()
    {
        return $this->positionCounter++;
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

        // query whether or not an option ID for the passed name is available
        if (isset($this->nameOptionIdMapping[$name])) {
            return $this->nameOptionIdMapping[$name];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Can\'t find option ID for name %s', $name));
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

        // query whether or not the passed price type is available
        if (isset($this->priceTypeMapping[$priceType])) {
            return $this->priceTypeMapping[$priceType];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Can\'t find price type %s', $priceType));
    }

    /**
     * Add's the mapping for the passed name => option ID.
     *
     * @param string  $name     The name of the option
     * @param integer $optionId The created option ID
     *
     * @return void
     */
    public function addNameOptionIdMapping($name, $optionId)
    {
        $this->nameOptionIdMapping[$name] = $optionId;
    }

    /**
     * Query whether or not the option with the passed name has already been created.
     *
     * @param string $name The option name to query for
     *
     * @return boolean TRUE if the option already exists, else FALSE
     */
    public function exists($name)
    {
        return isset($this->nameOptionIdMapping[$name]);
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
     * Persist's the passed product bundle option data and return's the ID.
     *
     * @param array $productBundleOption The product bundle option data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProductBundleOption($productBundleOption)
    {
        return $this->getSubject()->persistProductBundleOption($productBundleOption);
    }

    /**
     * Persist's the passed product bundle option value data.
     *
     * @param array $productBundleOptionValue The product bundle option value data to persist
     *
     * @return void
     */
    public function persistProductBundleOptionValue($productBundleOptionValue)
    {
        $this->getSubject()->persistProductBundleOptionValue($productBundleOptionValue);
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
