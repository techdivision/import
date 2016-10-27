<?php

/**
 * TechDivision\Import\Observers\Product\ProductVariationObserver
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
class ProductVariationObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // query whether or not, we've configurables
        if (!isset($headers[ColumnKeys::CONFIGURABLE_VARIATIONS]) ||
            !isset($row[$headers[ColumnKeys::CONFIGURABLE_VARIATIONS]]))
        {
            return;
        }

        // query whether or not, we've configurables
        if ($configurableVariations = $row[$headers[ColumnKeys::CONFIGURABLE_VARIATIONS]]) {
            // load the variation labels, if available
            $configurableVariationLabels = $row[$headers[ColumnKeys::CONFIGURABLE_VARIATION_LABELS]];

            // append the variation
            $this->addVariation(
                array(
                    'status'          => 0, // status
                    'uid'             => $this->getUid(), // uid
                    'variations'      => explode('|', $configurableVariations), // variations
                    'variationLabels' => explode('|', $configurableVariationLabels) // variation labels
                )
            );
        }

        // returns the row
        return $row;
    }

    /**
     * Return's the UID of the file to be imported.
     *
     * @return string The UID of the file to be importded
     */
    public function getUid()
    {
        return $this->getSubject()->getUid();
    }

    /**
     * Add the passed varation to the product with the
     * last entity ID.
     *
     * @param array $variation The product variations
     *
     * @return void
     * @see \Import\Csv\Actions\ProductImportBunchAction::getLastEntityId()
     */
    public function addVariation(array $variation)
    {
        $this->getSubject()->addVariation($variation);
    }
}
