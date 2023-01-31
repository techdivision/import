<?php

/**
 * TechDivision\Import\Callbacks\AbstractMultiselectCallback
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface;

/**
 * A callback implementation that converts the passed multiselect value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractMultiselectCallback extends AbstractEavAwareCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param \TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface|null $observer The observer
     *
     * @return mixed The modified value
     */
    public function handle(AttributeCodeAndValueAwareObserverInterface $observer = null)
    {

        // set the observer
        $this->setObserver($observer);

        // load the attribute code and value
        $attributeCode = $observer->getAttributeCode();
        $attributeValue = $observer->getAttributeValue();

        // query whether or not a value for the attibute with the diven code has been set
        if ($attributeValue == null || $attributeValue === '') {
            return;
        }

        // load the ID of the actual store
        $storeId = $this->getStoreId(StoreViewCodes::ADMIN);

        // explode the multiselect values
        $vals = $this->explode($attributeValue);

        // initialize the array for the mapped values
        $mappedValues = array();

        // convert the option values into option value ID's
        foreach ($vals as $val) {
            // try to load the attribute option value and add the option ID
            if ($eavAttributeOptionValue = $this->loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($this->getEntityTypeId(), $attributeCode, $storeId, $val)) {
                $mappedValues[] = $eavAttributeOptionValue[MemberNames::OPTION_ID];
                continue;
            }

            $message = sprintf(
                'Can\'t find multiselect option value "%s" for attribute "%s"',
                $val,
                $attributeCode
            );
            // query whether or not we're in strict moode
            if (!$this->isStrictMode()) {
                // log a warning and continue with the next value
                $this->getSystemLogger()->warning(
                    $this->appendExceptionSuffix($message)
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
                // continue with the next option value
                continue;
            }

            // throw an exception if the attribute is not available
            throw new \Exception(
                $this->appendExceptionSuffix($message)
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
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string|null $value The value to extract
     *
     * @return array The exploded values
     */
    protected function explode($value) : array
    {
        return $value === null || $value === '' ? array() : $this->getSubject()->explode($value, $this->getSubject()->getMultipleValueDelimiter());
    }
}
