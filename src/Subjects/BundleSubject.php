<?php

/**
 * TechDivision\Import\Subjects\BundleSubject
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A SLSB that handles the process to import product variants.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class BundleSubject extends AbstractSubject
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
     * The available stores.
     *
     * @var array
     */
    protected $stores = array();

    /**
     * The mapping for the SKUs to the created entity IDs.
     *
     * @var array
     */
    protected $skuEntityIdMapping = array();

    /**
     * The option name => option ID mapping.
     *
     * @var array
     */
    protected $nameOptionIdMapping = array();

    /**
     * The ID of the last created selection.
     *
     * @var integer
     */
    protected $childSkuSelectionIdMapping = array();

    /**
     * The position counter, if no position for the bundle selection has been specified.
     *
     * @var integer
     */
    protected $positionCounter = 1;

    /**
     * The mapping for the price type.
     *
     * @var array
     */
    protected $priceTypeMapping = array(
        'fixed'   => BundleSubject::PRICE_TYPE_FIXED,
        'percent' => BundleSubject::PRICE_TYPE_PERCENT
    );

    /**
     * Intializes the previously loaded global data for exactly one variants.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // load the entity manager and the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import process
        $status = $registryProcessor->getAttribute($this->serial);

        // load the stores we've initialized before
        $this->stores = $status['globalData'][RegistryKeys::STORES];

        // load the attribute set we've prepared intially
        $this->skuEntityIdMapping = $status['skuEntityIdMapping'];

        // prepare the callbacks
        parent::setUp();
    }

    /**
     * Clean up the global data after importing the bundles.
     *
     * @return void
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status of the actual import process
        // $registryProcessor->mergeAttributesRecursive($this->serial, array('bundles' => array($this->getUid() => array('status' => 1))));
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
     * Save's the mapping of the child SKU and the selection ID.
     *
     * @param string  $childSku    The child SKU of the selection
     * @param integer $selectionId The selection ID to save
     *
     * @return void
     */
    public function addChildSkuSelectionIdMapping($childSku, $selectionId)
    {
        $this->childSkuSelectionIdMapping[$childSku] = $selectionId;
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
        return $this->childSkuSelectionIdMapping[$childSku];
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

        // query weather or not the SKU has been mapped
        if (isset($this->skuEntityIdMapping[$sku])) {
            return $this->skuEntityIdMapping[$sku];
        }

        // throw an exception if the SKU has not been mapped yet
        throw new \Exception(sprintf('Found not mapped SKU %s', $sku));
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
     * Return's the store for the passed store code.
     *
     * @param string $storeCode The store code to return the store for
     *
     * @return array The requested store
     * @throws \Exception Is thrown, if the requested store is not available
     */
    public function getStoreByStoreCode($storeCode)
    {

        // query whether or not the store with the passed store code exists
        if (isset($this->stores[$storeCode])) {
            return $this->stores[$storeCode];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Found invalid store code %s', $storeCode));
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
     * Return's the last created option ID.
     *
     * @return integer $optionId The last created option ID
     */
    public function getLastOptionId()
    {
        return end($this->nameOptionIdMapping);
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
     * Persist's the passed product bundle option data and return's the ID.
     *
     * @param array $productBundleOption The product bundle option data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProductBundleOption($productBundleOption)
    {
        return $this->getProductProcessor()->persistProductBundleOption($productBundleOption);
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
        return $this->getProductProcessor()->persistProductBundleOptionValue($productBundleOptionValue);
    }

    /**
     * Persist's the passed product bundle selection data and return's the ID.
     *
     * @param array $productBundleOption The product bundle selection data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProductBundleSelection($productBundleSelection)
    {
        return $this->getProductProcessor()->persistProductBundleSelection($productBundleSelection);
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
        $this->getProductProcessor()->persistProductBundleSelectionPrice($productBundleSelectionPrice);
    }
}
