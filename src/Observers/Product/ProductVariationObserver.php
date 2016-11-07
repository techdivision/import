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
use TechDivision\Import\Observers\AbstractObserver;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;
use TechDivision\Import\Utils\ProductTypes;

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
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'variants';

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've configurables
        if (!isset($headers[ColumnKeys::CONFIGURABLE_VARIATIONS]) ||
            !isset($row[$headers[ColumnKeys::CONFIGURABLE_VARIATIONS]]))
        {
            return $row;
        }

        // query whether or not we've found a configurable product
        if ($row[$headers[ColumnKeys::PRODUCT_TYPE]] !== ProductTypes::CONFIGURABLE) {
            return $row;
        }

        // query whether or not, we've a configurable configuration
        if (!isset($row[$headers[ColumnKeys::CONFIGURABLE_VARIATIONS]])) {
            return $row;
        }

        // query whether or not, we've configurables
        if ($configurableVariations = $row[$headers[ColumnKeys::CONFIGURABLE_VARIATIONS]]) {
            // load the variation labels, if available
            $configurableVariationLabels = $row[$headers[ColumnKeys::CONFIGURABLE_VARIATION_LABELS]];

            // create an array with the variation labels (attribute code as key)
            $varLabels = array();
            foreach (explode('|', $configurableVariationLabels) as $variationLabel) {
                if (strstr($variationLabel, '=')) {
                    list ($key, $value) = explode('=', $variationLabel);
                    $varLabels[$key] = $value;
                }
            }

            // intialize the array for the variations
            $artefacts = array();

            // load the parent SKU from the row
            $parentSku = $row[$headers[ColumnKeys::SKU]];

            // iterate over all variations and import them
            foreach (explode('|', $configurableVariations) as $variation) {

                // sku=Configurable Product 48-option 2,configurable_variation=option 2
                list ($sku, $option) = explode(',', $variation);

                // explode the variations child ID as well as option code and value
                list (, $childSku) = explode('=', $sku);
                list ($optionCode, $optionValue) = explode('=', $option);

                // load the apropriate variation label
                $varLabel = '';
                if (isset($varLabels[$optionCode])) {
                    $varLabel = $varLabels[$optionCode];
                }

                // append the product variation
                $artefacts[] = array(
                    ColumnKeys::STORE_VIEW_CODE         => $row[$headers[ColumnKeys::STORE_VIEW_CODE]],
                    ColumnKeys::VARIANT_PARENT_SKU      => $parentSku,
                    ColumnKeys::VARIANT_CHILD_SKU       => $childSku,
                    ColumnKeys::VARIANT_OPTION_VALUE    => $optionValue,
                    ColumnKeys::VARIANT_VARIATION_LABEL => $varLabel
                );
            }

            // append the variations to the subject
            $this->addArtefacts($artefacts);
        }

        // returns the row
        return $row;
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(ProductVariationObserver::ARTEFACT_TYPE, $artefacts);
    }
}
