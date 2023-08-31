<?php

/**
 * TechDivision\Import\Callbacks\MultiselectValidatorCallback
 *
 * PHP version 7
 *
 * @author    Patrick Mehringer <p.mehringer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Attribute\Utils\ColumnKeys;

/**
 * A callback implementation that validates the values of array attributes.
 *
 * @author    Patrick Mehringer <p.mehringer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class EmptyValueValidatorCallback extends IndexedArrayValidatorCallback
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
        // load the subject instance
        $subject = $this->getSubject();

        // explode the additional attributes
        if ($this->isNullable($attributeValue)) {
            return;
        }

        // extract the option values
        $optionValues = $subject->explode($attributeValue, ',');
        if (!is_array($optionValues)) {
            return;
        }

        // iterate over the attribute value options
        foreach ($optionValues as $optionValue) {
            // query whether or not the value is valid
            if (trim($optionValue) === '') {
                throw new \InvalidArgumentException(
                    sprintf("Found empty array value in '%s' for attribute or property with code '%s'", $attributeValue, $subject->getValue(ColumnKeys::ATTRIBUTE_CODE))
                );
            }
        }
    }
}
