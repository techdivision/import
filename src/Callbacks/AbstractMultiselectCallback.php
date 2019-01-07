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
abstract class AbstractMultiselectCallback extends AbstractEavAwareCallback
{

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

        // load the ID of the actual store
        $storeId = $this->getStoreId(StoreViewCodes::ADMIN);

        // explode the multiselect values
        $vals = explode('|', $attributeValue);

        // initialize the array for the mapped values
        $mappedValues = array();

        // convert the option values into option value ID's
        foreach ($vals as $val) {
            // try to load the attribute option value and add the option ID
            if ($eavAttributeOptionValue = $this->loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($this->getEntityTypeId(), $attributeCode, $storeId, $val)) {
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
}
