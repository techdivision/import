<?php

/**
 * TechDivision\Import\Observers\Product\VariantObserver
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class VariantObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // extract the parent/child ID as well as option value and variation label from the row
        $parentSku = $row[$headers[ColumnKeys::VARIANT_PARENT_SKU]];
        $childSku = $row[$headers[ColumnKeys::VARIANT_CHILD_SKU]];
        $optionValue = $row[$headers[ColumnKeys::VARIANT_OPTION_VALUE]];
        $variationLabel = $row[$headers[ColumnKeys::VARIANT_VARIATION_LABEL]];

        // load parent/child IDs
        $parentId = $this->mapSkuToEntityId($parentSku);
        $childId = $this->mapSkuToEntityId($childSku);

        // create the product relation
        $this->persistProductRelation(array($parentId, $childId));
        $this->persistProductSuperLink(array($childId, $parentId));

        // load the store ID
        $store = $this->getStoreByStoreCode($row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::ADMIN);
        $storeId = $store[MemberNames::STORE_ID];

        // load the EAV attribute
        $eavAttribute = $this->getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);

        // query whether or not, the parent ID have changed
        if (!$this->isParentId($parentId)) {
            // preserve the parent ID
            $this->setParentId($parentId);

            // load the attribute ID
            $attributeId = $eavAttribute[MemberNames::ATTRIBUTE_ID];
            // if yes, create the super attribute load the ID of the new super attribute
            $productSuperAttributeId = $this->persistProductSuperAttribute(array($parentId, $attributeId, 0));

            // query whether or not we've to create super attribute labels
            if (empty($variationLabel)) {
                $variationLabel = $eavAttribute[MemberNames::FRONTENT_LABEL];
            }

            // prepare the super attribute label
            $params = array($productSuperAttributeId, $storeId, 0, $variationLabel);
            // save the super attribute label
            $this->persistProductSuperAttributeLabel($params);
        }

        // returns the row
        return $row;
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
     * Return's TRUE if the passed ID is the parent one.
     *
     * @param integer $parentID The parent ID to check
     *
     * @return boolean TRUE if the passed ID is the parent one
     */
    public function isParentId($parentId)
    {
        return $this->getParentId() === $parentId;
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
        $this->getSubject()->setParentId($parentId);
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    public function getParentId()
    {
        return $this->getSubject()->getParentId();
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
     * Return's an array with the available stores.
     *
     * @return array The available stores
     */
    public function getStores()
    {
        return $this->getSubject()->getStores();
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
        return $this->getSubject()->getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);
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
        return $this->getSubject()->persistProductRelation($productRelation);
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
        return $this->getSubject()->persistProductSuperLink($productSuperLink);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return void
     */
    public function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getSubject()->persistProductSuperAttribute($productSuperAttribute);
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
        return $this->getSubject()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
