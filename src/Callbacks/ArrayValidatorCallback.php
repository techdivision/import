<?php

/**
 * TechDivision\Import\Callbacks\ArrayValidatorCallback
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Loaders\LoaderInterface;

/**
 * Array validator callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ArrayValidatorCallback extends AbstractValidatorCallback
{

    /**
     * The flag to query whether or not the value can be empty.
     *
     * @var boolean
     */
    protected $nullable = false;

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader   The loader instance to load the validations with
     * @param boolean                                      $nullable The flag to decide whether or not the value can be empty
     */
    public function __construct(LoaderInterface $loader, $nullable = false)
    {

        // pass the loader to the parent instance
        parent::__construct($loader);

        // initialize the flag with the passed value
        $this->nullable = $nullable;
    }

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

        // if the passed value is in the array, return immediately
        if (in_array($attributeValue, $validations) || $this->isNullable($attributeValue)) {
            return;
        }

        // throw an exception if NO allowed values have been configured
        if (sizeof($validations) === 0) {
            throw new \InvalidArgumentException(
                sprintf('Missing configuration value for custom validation of attribute "%s"', $attributeCode)
            );
        }

        // throw an exception if the value is NOT in the array
        throw new \InvalidArgumentException(
            sprintf(
                'Found invalid value "%s" for column "%s" (must be one of: "%s")',
                $attributeValue,
                $attributeCode,
                implode(', ', $validations)
            )
        );
    }

    /**
     * Query whether or not the passed value IS empty and empty values are allowed.
     *
     * @param string $attributeValue The attribute value to query for
     *
     * @return boolean TRUE if empty values are allowed and the passed value IS empty
     */
    protected function isNullable($attributeValue)
    {
        return $this->nullable && ($attributeValue === '' || $attributeValue === null);
    }
}
