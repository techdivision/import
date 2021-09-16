<?php

/**
 * TechDivision\Import\Callbacks\BooleanValidatorCallback
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

/**
 * A callback implementation that validates the option values of EAV boolean attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class BooleanValidatorCallback extends ArrayValidatorCallback
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
        if ($this->isNullable($optionValues = $subject->explode($attributeValue, '='))) {
            return;
        }

        // load the validations
        $validations = $this->getValidations($attributeCode);

        // iterate over the attributes and append them to the row
        foreach ($optionValues as $optionValue) {
            // query whether or not a boolean value has been specified
            if (in_array(strtolower($optionValue), $validations)) {
                continue;
            }

            // throw an exception if the value is NOT in the array
            throw new \InvalidArgumentException(
                sprintf('Found invalid boolean value "%s" for attribute with code "%s"', $optionValue, $attributeCode)
            );
        }
    }
}
