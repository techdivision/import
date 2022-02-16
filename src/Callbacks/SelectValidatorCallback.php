<?php

/**
 * TechDivision\Import\Callbacks\SelectValidatorCallback
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
 * A callback implementation that validates the option values of EAV select attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SelectValidatorCallback extends IndexedArrayValidatorCallback
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

        // explode the additional attributes
        if ($this->isNullable($attributeValue)) {
            return;
        }

        // load the validations for the attribute with the passed code
        $validations = $this->getValidations($attributeCode);

        // query whether or not the value is valid
        if (in_array($attributeValue, $validations)) {
            return;
        }

        $message = sprintf('Found invalid option value "%s" for select attribute with code "%s"', $attributeValue, $attributeCode);

        if ($this->hasHandleStrictMode($attributeCode, $message)) {
            return;
        }

        // throw an exception if the value is NOT in the array
        throw new \InvalidArgumentException($message);
    }
}
