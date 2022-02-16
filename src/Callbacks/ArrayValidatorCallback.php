<?php

/**
 * TechDivision\Import\Callbacks\ArrayValidatorCallback
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

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * Array validator callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The flag to query whether or not the value has to be validated on the main row only.
     *
     * @var boolean
     */
    protected $mainRowOnly = false;

    /**
     * The flag to query whether or not the value has to be ignored global strict mode configuration.
     *
     * @var boolean
     */
    protected $ignoreStrictMode = false;

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader           The loader instance to load the validations with
     * @param boolean                                      $nullable         The flag to decide whether or not the value can be empty
     * @param boolean                                      $mainRowOnly      The flag to decide whether or not the value has to be validated on the main row only
     * @param boolean                                      $ignoreStrictMode The flag to query whether or not the value has to be ignored global strict mode configuration.
     */
    public function __construct(LoaderInterface $loader, $nullable = false, $mainRowOnly = false, $ignoreStrictMode = true)
    {

        // pass the loader to the parent instance
        parent::__construct($loader);

        // initialize the flags with the passed values
        $this->nullable = $nullable;
        $this->mainRowOnly = $mainRowOnly;
        $this->ignoreStrictMode = $ignoreStrictMode;
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
        if (in_array($attributeValue, $validations)) {
            return;
        }

        // query whether or not the passed value IS empty and empty
        // values are allowed
        if ($this->isNullable($attributeValue)) {
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

        // query whether or not the passed value IS empty
        if ($attributeValue === '' || $attributeValue === null) {
            // z1: value can NEVER be empty
            if ($this->nullable === false && $this->mainRowOnly === false) {
                return false;
            }

            // z3: value can ALWAYS be empty
            if ($this->nullable === true && $this->mainRowOnly === false) {
                return true;
            }

            // z2: value MUST NOT be empty in the main row
            if ($this->nullable === false && $this->mainRowOnly === true) {
                return $this->isMainRow() ? false : true;
            }

            // z4: value can ONLY be empty in the main row
            if ($this->nullable === true && $this->mainRowOnly === true) {
                return $this->isMainRow() ? true : false;
            }
        }

        // if not, return TRUE immediately
        return false;
    }

    /**
     * @param string $attributeCode The attribute value to query for
     * @param string $message       Message for validation error
     * @return bool
     */
    protected function hasHandleStrictMode($attributeCode, $message)
    {
        if ($this->ignoreStrictMode) {
            return false;
        }
        if (!$this->getSubject()->isStrictMode()) {
            $this->getSubject()
                ->getSystemLogger()
                ->warning($this->getSubject()->appendExceptionSuffix($message));
            $this->getSubject()->mergeStatus(
                array(
                    RegistryKeys::NO_STRICT_VALIDATIONS => array(
                        basename($this->getSubject()->getFilename()) => array(
                            $this->getSubject()->getLineNumber() => array(
                                $attributeCode  => $message
                            )
                        )
                    )
                )
            );
            return true;
        }

        return false;
    }
}
