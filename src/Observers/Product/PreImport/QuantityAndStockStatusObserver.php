<?php

/**
 * TechDivision\Import\Observers\Product\PreImport\QuantityAndStockStatusObserver
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

namespace TechDivision\Import\Observers\Product\PreImport;

use TechDivision\Import\Utils\ColumnKeys;
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
class QuantityAndStockStatusObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        /*
        $qty = (float) $row[$this->headers[ColumnKeys::QTY]];
        $isInStock = (integer) $row[$this->headers[ColumnKeys::IS_IN_STOCK]];

        $this->getSystemLogger()->info("Found qty $qty and is_in_stock $isInStock");

        $quantityAndStockStatus = 0;
        if ($qty > 0 && $isInStock === 1) {
            $quantityAndStockStatus = 1;
        }
        */

        // try to load the appropriate key for the stock status
        if (isset($headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS])) {
            $newKey = $headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS];
        } else {
            $headers[ColumnKeys::QUANTITY_AND_STOCK_STATUS] = $newKey = sizeof($headers);
        }

        // append/replace the stock status
        $row[$newKey] = 1;

        // update the header information
        $this->setHeaders($headers);

        // return the prepared row
        return $row;
    }
}
