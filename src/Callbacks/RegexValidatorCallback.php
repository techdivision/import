<?php

/**
 * TechDivision\Import\Callbacks\RegexValidatorCallback
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

/**
 * Regex validator callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RegexValidatorCallback extends IndexedArrayValidatorCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {

        // the validations for the attribute with the given code
        $validations = $this->getValidations($attributeCode);

        // throw an exception if NO allowed values have been configured
        if (sizeof($validations) === 0) {
            throw new \InvalidArgumentException(
                sprintf('Missing configuration value for custom validation of attribute "%s"', $attributeCode)
            );
        }

        // if the passed value is in the array, return immediately
        foreach ($validations as $pattern) {
            // validates the attribute value against the passed patterns
            if (preg_match($pattern, $attributeValue)) {
                continue;
            }

            // throw an exception if the value is NOT in the array
            throw new \InvalidArgumentException(
                sprintf(
                    'Found invalid value "%s" for column "%s" (must match pattern: "%s")',
                    $attributeValue,
                    $attributeCode,
                    $pattern
                )
            );
        }
    }
}
