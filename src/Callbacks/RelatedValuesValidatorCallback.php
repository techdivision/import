<?php

/**
 * TechDivision\Import\Callbacks\relatedValuesValidatorCallback
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Grouped\Utils\ColumnKeys as GroupedColumnsKeys;
use TechDivision\Import\Product\Bundle\Utils\ColumnKeys as BundleColumnsKeys;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys as VariantColumnsKeys;

/**
 * A callback implementation that validates the a list of values.
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RelatedValuesValidatorCallback extends ArrayValidatorCallback
{

    /**
     * Will be invoked by the observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {
        // explode the values
        $delimiter = $attributeCode === GroupedColumnsKeys::ASSOCIATED_SKUS ? ',' : '|';

        $values = $this->getSubject()->explode($attributeValue, $delimiter);


        // query whether or not an empty value is allowed
        if ($this->isNullable($values)) {
            return;
        }

        // load the validations for the column
        $validations = $this->getValidations();
        // load the parent SKU from the row
        $parentSku = $this->getSubject()->getValue(ColumnKeys::SKU);

        $skuErrors = [];
        // iterate over the values and validate them
        foreach ($values as $value) {
            $skuValue = '';
            if ($attributeCode === GroupedColumnsKeys::ASSOCIATED_SKUS) {
                list($skuValue) = $this->getSubject()->explode($value, '=');
            } elseif (in_array($attributeCode, [VariantColumnsKeys::CONFIGURABLE_VARIATIONS, BundleColumnsKeys::BUNDLE_VALUES])) {
                $value = $this->getSubject()->explode($value, ',');
                foreach ($value as $subValue) {
                    if (str_starts_with($subValue, ColumnKeys::SKU)) {
                        list(, $skuValue) = $this->getSubject()->explode($subValue, '=');
                    }
                }
            }

            // query whether or not the value is valid
            if (in_array($skuValue, $validations)) {
                continue;
            }
            array_push($skuErrors, $skuValue);
        }
        if (count($skuErrors) > 0) {
            // throw an exception if the value is NOT in the array
            throw new \InvalidArgumentException(
                sprintf(
                    'Found invalid SKUs "%s" to be related to grouped product with SKU "%s"',
                    implode(',', $skuErrors),
                    $parentSku
                )
            );
        }
    }
}
