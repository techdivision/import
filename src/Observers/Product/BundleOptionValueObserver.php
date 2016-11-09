<?php

/**
 * TechDivision\Import\Observers\Product\BundleOptionValueObserver
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
class BundleOptionValueObserver extends AbstractProductImportObserver
{

    protected $optionValueStoreMapping = array();

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // load the product bundle option name
        $name = $row[$headers[ColumnKeys::BUNDLE_VALUE_NAME]];

        // initialize the store view code
        $storeViewCode = $row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::ADMIN;

        // load the store/website ID
        $store = $this->getStoreByStoreCode($storeViewCode);
        $storeId = $store[MemberNames::STORE_ID];

        // load the actual option ID
        $optionId = $this->getLastOptionId();

        if (isset($this->optionValueStoreMapping[$optionId]) &&
            in_array($storeId, $this->optionValueStoreMapping[$optionId])
        ) {
            return $row;
        }

        // save the product bundle option value
        $this->persistProductBundleOptionValue(array($optionId, $storeId, $name));

        $this->optionValueStoreMapping[$optionId][] = $storeId;

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
     * Return's the option ID for the passed name.
     *
     * @param string $name The name to return the option ID for
     *
     * @return integer The option ID for the passed name
     * @throws \Exception Is thrown, if no option ID for the passed name is available
     */
    public function getOptionIdForName($name)
    {
        return $this->getSubject()->getOptionIdForName($name);
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
}
