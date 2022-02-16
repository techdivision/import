<?php

/**
 * TechDivision\Import\Callbacks\CommaDelimiterSkuRelationsValidatorCallback
 *
 * PHP version 7
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A callback implementation that validates the a list of values.
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CommaDelimiterSkuRelationsValidatorCallback extends ArrayValidatorCallback
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
        $values = $this->getSubject()->explode($attributeValue, $this->getAttributeValueDelimiter());
        // query whether or not an empty value is allowed
        if ($this->isNullable($values)) {
            return;
        }

        // load the validations for the column
        $validations = $this->getValidations();
        // load the parent SKU from the row
        $rowSku = $this->getSubject()->getValue(ColumnKeys::SKU);
        $rowProductType = $this->getSubject()->getValue(ColumnKeys::PRODUCT_TYPE);

        $skuErrors = [];
        // iterate over the values and validate them
        foreach ($values as $value) {
            // First element always SKU
            $value = $this->explodeDetailsFromValue($value);

            // query whether or not the value is valid
            if (in_array($value, $validations)) {
                continue;
            }
            // collect single sku for error
            array_push($skuErrors, $value);
        }
        if (count($skuErrors) > 0) {
            $message =  sprintf(
                'Found invalid SKUs "%s" to be related to %s product with SKU "%s"',
                implode(',', $skuErrors),
                $rowProductType,
                $rowSku
            );

            if ($this->hasHandleStrictMode($attributeCode, $message)) {
                return;
            }

            // throw an exception if the value is NOT in the array and strict mode on
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * @return string
     */
    protected function getAttributeValueDelimiter()
    {
        return ',';
    }

    /**
     * @param string $value the value to explode
     * @return string
     */
    protected function explodeDetailsFromValue($value)
    {
        // First element always SKU
        list($firstValue) = $this->getSubject()->explode($value, '=');
        return $firstValue;
    }
}
