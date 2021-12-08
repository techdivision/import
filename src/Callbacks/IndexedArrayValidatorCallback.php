<?php

/**
 * TechDivision\Import\Callbacks\IndexedArrayValidatorCallback
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
 * Array validator callback implementation that expects an indexed array with
 * validations, whereas the key is the attribute name.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class IndexedArrayValidatorCallback extends ArrayValidatorCallback
{

    /**
     * Returns the validations for the attribute with the passed code.
     *
     * @param string|null $attributeCode The code of the attribute to return the validations for
     *
     * @return array The allowed values for the attribute with the passed code
     */
    protected function getValidations($attributeCode = null)
    {

        // query whether or not if allowed values have been specified
        if (isset($this->validations[$attributeCode])) {
            return $this->validations[$attributeCode];
        }

        // return an empty array, if NOT
        return array();
    }
}
