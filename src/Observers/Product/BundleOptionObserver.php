<?php

/**
 * TechDivision\Import\Observers\Product\BundleOptionObserver
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
class BundleOptionObserver extends AbstractProductImportObserver
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
        }

        // returns the row
        return $row;
    }

    /**
     * Reset the position counter to 1.
     *
     * @return void
     */
    public function resetPositionCounter()
    {
        $this->getSubject()->resetPositionCounter();
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
        $this->getSubject()->addNameOptionIdMapping($name, $optionId);
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
        return $this->getSubject()->exists($name);
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
}
