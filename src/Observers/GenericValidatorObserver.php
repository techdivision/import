<?php

/**
 * TechDivision\Import\Observers\GenericValidatorObserver
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * Observer that invokes the callbacks to validate the actual row.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericValidatorObserver extends AbstractObserver
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // load the available header names
        $headerNames = array_keys($this->getHeaders());

        // iterate over the custom validations
        foreach ($headerNames as $headerName) {
            // map the header name to the attribute code, if an mapping is available
            $attributeCode = $this->mapAttributeCodeByHeaderMapping($headerName);
            // load the attribute value from the row
            $attributeValue = $this->getValue($attributeCode);
            // load the callbacks for the actual attribute code
            $callbacks = $this->getCallbacksByType($attributeCode);
            // invoke the registered callbacks
            foreach ($callbacks as $callback) {
                try {
                    $callback->handle($attributeCode, $attributeValue);
                } catch (\InvalidArgumentException $iea) {
                    // add the the validation result to the status
                    $this->mergeStatus(
                        array(
                            RegistryKeys::VALIDATIONS => array(
                                basename($this->getFilename()) => array(
                                    $this->getSubject()->getLineNumber() => array(
                                        $attributeCode => $iea->getMessage()
                                    )
                                )
                            )
                        )
                    );
                }
            }
        }
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    protected function mapAttributeCodeByHeaderMapping($attributeCode)
    {
        return $this->getSubject()->mapAttributeCodeByHeaderMapping($attributeCode);
    }

    /**
     * Return's the array with callbacks for the passed type.
     *
     * @param string $type The type of the callbacks to return
     *
     * @return array The callbacks
     */
    protected function getCallbacksByType($type)
    {
        return $this->getSubject()->getCallbacksByType($type);
    }
}
