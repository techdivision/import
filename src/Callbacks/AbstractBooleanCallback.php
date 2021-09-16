<?php

/**
 * TechDivision\Import\Callbacks\AbstractBooleanCallback
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

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface;

/**
 * A callback implementation that converts the passed boolean value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractBooleanCallback extends AbstractCallback
{

    /**
     * Array with the string => boolean mapping.
     *
     * @var array
     */
    protected $booleanValues = array(
        'true'  => 1,
        'yes'   => 1,
        '1'     => 1,
        'false' => 0,
        'no'    => 0,
        '0'     => 0
    );

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

        // return NULL, if the value is empty
        if ($attributeValue === null || $attributeValue === '') {
            return;
        }

        // query whether or not, the passed value can be mapped to a boolean representation
        if (isset($this->booleanValues[strtolower($attributeValue)])) {
            return (boolean) $this->booleanValues[strtolower($attributeValue)];
        }

        // query whether or not we're in debug mode
        if ($this->isDebugMode()) {
            // log a warning and continue with the next value
            $this->getSystemLogger()->warning(
                $this->appendExceptionSuffix(
                    sprintf(
                        'Can\'t map option value "%s" for attribute %s to a boolean representation',
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

            // return NULL, if NO value can be mapped to a boolean representation
            return;
        }

        // throw an exception if the attribute is not available
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf(
                    'Can\'t map option value "%s" for attribute "%s" to a boolean representation',
                    $attributeValue,
                    $attributeCode
                )
            )
        );
    }
}
