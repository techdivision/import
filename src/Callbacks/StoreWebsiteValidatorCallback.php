<?php

/**
 * TechDivision\Import\Callbacks\ArrayValidatorCallback
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2024 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * storeview validator callback implementation.
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2024 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StoreWebsiteValidatorCallback extends ArrayValidatorCallback
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
     * The store websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * Initializes the callback with the loader instance.
     *
     * @param LoaderInterface          $storeLoader      The loader instance to load the validations with
     * @param ImportProcessorInterface $importProcessor  The import processor instance
     * @param boolean                  $nullable         The flag to decide whether or not the value can be empty
     * @param boolean                  $mainRowOnly      The flag to decide whether or not the value has to be validated on the main row only
     * @param boolean                  $ignoreStrictMode The flag to query whether or not the value has to be ignored global strict mode configuration.
     */
    public function __construct(LoaderInterface $storeLoader, ImportProcessorInterface $importProcessor, $nullable = false, $mainRowOnly = false, $ignoreStrictMode = true)
    {

        // pass the loader to the parent instance
        parent::__construct($storeLoader);

        // initialize the flags with the passed values
        $this->nullable = $nullable;
        $this->mainRowOnly = $mainRowOnly;
        $this->ignoreStrictMode = $ignoreStrictMode;

        // initialize the array with the store websites
        foreach ($importProcessor->getStoreWebsites() as $storeWebsite) {
            $this->storeWebsites[$storeWebsite[MemberNames::CODE]] = $storeWebsite[MemberNames::WEBSITE_ID];
        }
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
        // query whether or not the passed value IS empty and empty
        // values are allowed
        if ($this->isNullable($attributeValue)) {
            return;
        }

        // the validations for the attribute with the given code
        $validations = $this->getValidations($attributeCode);

        $website = $this->load();
        $productWebsite = $this->getSubject()->getValue('product_websites');

        if ($validations[$attributeValue][MemberNames::WEBSITE_ID] !== $website[$productWebsite]) {
            $message = sprintf(
                'The store "%s" does not belong to the website "%s" . Please check your data.',
                $attributeValue,
                $productWebsite
            );

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
        }
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->storeWebsites;
    }
}
