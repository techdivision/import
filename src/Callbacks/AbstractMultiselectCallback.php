<?php

/**
 * TechDivision\Import\Callbacks\AbstractMultiselectCallback
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Services\EavAwareProcessorInterface;
use TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface;

/**
 * A callback implementation that converts the passed multiselect value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractMultiselectCallback extends AbstractCallback
{

    /**
     * The EAV aware processor.
     *
     * @var \TechDivision\Import\Services\EavAwareProcessorInterface
     */
    protected $eavAwareProcessor;

    /**
     * Initialize the callback with the passed processor instance.
     *
     * @param \TechDivision\Import\Services\EavAwareProcessorInterface $eavAwareProcessor The processor instance
     */
    public function __construct(EavAwareProcessorInterface $eavAwareProcessor)
    {
        $this->eavAwareProcessor = $eavAwareProcessor;
    }

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer
     *
     * @return mixed The modified value
     */
    public function handle(AttributeCodeAndValueAwareObserverInterface $observer)
    {

        // set the observer
        $this->setObserver($observer);

        // load the attribute code and value
        $attributeCode = $observer->getAttributeCode();
        $attributeValue = $observer->getAttributeValue();

        // explode the multiselect values
        $vals = explode('|', $attributeValue);

        // initialize the array for the mapped values
        $mappedValues = array();

        // convert the option values into option value ID's
        foreach ($vals as $val) {
            // load the ID of the actual store
            $storeId = $this->getStoreId(StoreViewCodes::ADMIN);

            // try to load the attribute option value and add the option ID
            if ($eavAttributeOptionValue = $this->loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $val)) {
                $mappedValues[] = $eavAttributeOptionValue[MemberNames::OPTION_ID];
                continue;
            }

            // query whether or not we're in debug mode
            if ($this->isDebugMode()) {
                // log a warning and continue with the next value
                $this->getSystemLogger()->warning(
                    $this->appendExceptionSuffix(
                        sprintf(
                            'Can\'t find multiselect option value "%s" for attribute %s',
                            $val,
                            $attributeCode
                        )
                    )
                );

                // add the missing option value to the registry
                $this->mergeAttributesRecursive(
                    array(
                        RegistryKeys::MISSING_OPTION_VALUES => array(
                            $attributeCode => array(
                                $val => array(
                                    $this->raiseCounter($val),
                                    array($this->getUniqueIdentifier() => true)
                                )
                            )
                        )
                    )
                );

                // continue with the next option value
                continue;
            }

            // throw an exception if the attribute is not available
            throw new \Exception(
                $this->appendExceptionSuffix(
                    sprintf(
                        'Can\'t find multiselect option value "%s" for attribute %s',
                        $val,
                        $attributeCode
                    )
                )
            );
        }

        // return NULL, if NO value can be mapped to an option
        if (sizeof($mappedValues) === 0) {
            return;
        }

        // re-concatenate and return the values
        return implode(',', $mappedValues);
    }

    /**
     * Return's the EAV aware processor instance.
     *
     * @return \TechDivision\Import\Services\EavAwareProcessorInterface The processor instance
     */
    protected function getEavAwareProcessor()
    {
        return $this->eavAwareProcessor;
    }

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    protected function getRowStoreId($default = null)
    {
        return $this->getSubject()->getRowStoreId($default);
    }

    /**
     * Load's and return's the EAV attribute option value with the passed code, store ID and value.
     *
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    protected function loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value)
    {
        return $this->getEavAwareProcessor()->loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value);
    }
}
