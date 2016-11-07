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
