<?php

/**
 * TechDivision\Import\Callbacks\SelectCallback
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
 * A callback implementation that converts the passed select value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractSelectCallback extends AbstractCallback
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

        // load the store ID
        $storeId = $this->getStoreId(StoreViewCodes::ADMIN);

        // try to load the attribute option value and return the option ID
        if ($eavAttributeOptionValue = $this->loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $attributeValue)) {
            return $eavAttributeOptionValue[MemberNames::OPTION_ID];
        }

        // query whether or not we're in debug mode
        if ($this->isDebugMode()) {
            // log a warning and return immediately
            $this->getSystemLogger()->warning(
                $this->appendExceptionSuffix(
                    sprintf(
                        'Can\'t find select option value "%s" for attribute %s',
                        $attributeValue,
                        $attributeCode
                    )
                )
            );

            // add the missing option value to the registry
            $this->mergeAttributesRecursive(
                array(
                    RegistryKeys::MISSING_OPTION_VALUES => array(
                        $attributeCode => array(
                            $attributeValue => array(
                                $this->raiseCounter($attributeValue),
                                array($this->getUniqueIdentifier() => true)
                            )
                        )
                    )
                )
            );

            // return NULL, if the value can't be mapped to an option
            return;
        }

        // throw an exception if the attribute is NOT
        // available and we're not in debug mode
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf(
                    'Can\'t find select option value "%s" for attribute %s',
                    $attributeValue,
                    $attributeCode
                )
            )
        );
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
        return $this->getSubject()->loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value);
    }
}
