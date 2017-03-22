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
use TechDivision\Import\Utils\FrontendInputTypes;

/**
 * A callback implementation that converts the passed select value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SelectCallback extends AbstractCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param string $attributeCode  The code of the attribute the passed value is for
     * @param mixed  $attributeValue The value to handle
     *
     * @return mixed|null The modified value
     * @see \TechDivision\Import\Callbacks\CallbackInterface::handle()
     */
    public function handle($attributeCode, $attributeValue)
    {

        // load the store ID
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // try to load the attribute option value and return the option ID
        if ($eavAttributeOptionValue = $this->getEavAttributeOptionValueByOptionValueAndStoreId($attributeValue, $storeId)) {
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
                            $attributeValue => FrontendInputTypes::SELECT
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
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    protected function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getSubject()->getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }
}
