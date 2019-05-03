<?php

/**
 * TechDivision\Import\Utils\ConfigurationUtil
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

namespace TechDivision\Import\Utils;

/**
 * Utility class containing the configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationUtil
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Prepare's the arguments for the passed reflection class by applying the values from the passed configuration array
     * to the apropriate arguments. If no value is found in the configuration, the constructor argument's default value is
     * used.
     *
     * @param \ReflectionClass $reflectionClass The reflection class to prepare the arguments for
     * @param array            $params          The constructor arguments from the configuration
     *
     * @return array The array with the constructor arguements
     */
    public static function prepareConstructorArgs(\ReflectionClass $reflectionClass, array $params)
    {

        // prepare the array for the initialized arguments
        $initializedParams = array();

        // prepare the array for the arguments in camel case (in configuration we use a '-' notation)
        $paramsInCamelCase = array();

        // convert the configuration keys to camelcase
        foreach ($params as $key => $value) {
            $paramsInCamelCase[lcfirst(str_replace('-', '', ucwords($key, '-')))] = $value;
        }

        // load the constructor's reflection parameters
        $constructorParameters = $reflectionClass->getConstructor()->getParameters();

        // prepare the arguments by applying the values from the configuration
        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($constructorParameters as $reflectionParameter) {
            if (isset($paramsInCamelCase[$paramName = $reflectionParameter->getName()])) {
                $initializedParams[$paramName] = $paramsInCamelCase[$paramName];
            } elseif ($reflectionParameter->isOptional()) {
                $initializedParams[$paramName] = $reflectionParameter->getDefaultValue();
            } else {
                $initializedParams[$paramName] = null;
            }
        }

        // return the array with the prepared arguments
        return $initializedParams;
    }
}
