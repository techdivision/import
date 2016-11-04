<?php

/**
 * TechDivision\Import\Subjects\VariantSubject
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
class VariantSubject extends AbstractSubject
{

    /**
     * The mapping for the SKUs to the created entity IDs.
     *
     * @var array
     */
    protected $skuEntityIdMapping = array();

    /**
     * The ID of the parent product to relate the variant with.
     *
     * @var integer
     */
    protected $parentId;

    /**
     * The available stores.
     *
     * @var array
     */
    protected $stores = array();

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

        // load the EAV attributes we've prepared initially
        $this->eavAttributes = $status['globalData'][RegistryKeys::EAV_ATTRIBUTES];

        // load the stores we've initialized before
        $this->stores = $status['globalData'][RegistryKeys::STORES];

        // load the attribute set we've prepared intially
        $this->skuEntityIdMapping = $status['skuEntityIdMapping'];

        // prepare the callbacks
        parent::setUp();
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @return void
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status of the actual import process
        // $registryProcessor->mergeAttributesRecursive($this->serial, array('variations' => array($this->getUid() => array('status' => 1))));
    }

    /**
     * Set's the ID of the parent product to relate the variant with.
     *
     * @param integer $parentId The ID of the parent product
     *
     * @return void
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Return's an array with the available stores.
     *
     * @return array The available stores
     */
    public function getStores()
    {
        return $this->stores;
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
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function getEavAttributeByOptionValueAndStoreId($optionValue, $storeId)
    {
        return $this->getProductProcessor()->getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $productRelation The product relation data to persist
     *
     * @return void
     */
    public function persistProductRelation($productRelation)
    {
        return $this->getProductProcessor()->persistProductRelation($productRelation);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink)
    {
        return $this->getProductProcessor()->persistProductSuperLink($productSuperLink);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getProductProcessor()->persistProductSuperAttribute($productSuperAttribute);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductProcessor()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
