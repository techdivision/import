<?php

/**
 * TechDivision\Import\Observers\GenericValidationObserver
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Observer that invokes the callbacks to validate the actual row.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericValidationObserver extends AbstractObserver implements ObserverFactoryInterface
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Array with virtual column name mappings (this is a temporary
     * solution till techdivision/import#179 as been implemented).
     *
     * @var array
     */
    protected $reverseHeaderMappings = array();

    /**
     * Initializes the observer with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // initialize the array for the reverse header mappings
        $this->reverseHeaderMappings = array_flip($subject->getHeaderMappings());

        // return the intialized instance
        return $this;
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
            // load the attribute value from the row
            $attributeValue = $this->getValue($headerName);
            // reverse map the header name to the original column name
            $columnName = $this->reverseMapHeaderNameToColumnName($headerName);
            // load the callbacks for the actual attribute code
            $callbacks = $this->getCallbacksByType($columnName);
            // invoke the registered callbacks
            foreach ($callbacks as $callback) {
                try {
                    $callback->handle($columnName, $attributeValue);
                } catch (\InvalidArgumentException $iea) {
                    // add the the validation result to the status
                    $this->mergeStatus(
                        array(
                            RegistryKeys::VALIDATIONS => array(
                                basename($this->getFilename()) => array(
                                    $this->getSubject()->getLineNumber() => array(
                                        $columnName => $iea->getMessage()
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
     * Reverse map the passed header name, to the original column name.
     *
     * @param string $headerName The header name to reverse map
     *
     * @return string The original column name
     */
    protected function reverseMapHeaderNameToColumnName(string $headerName) : string
    {
        return $this->reverseHeaderMappings[$headerName] ?? $headerName;
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
