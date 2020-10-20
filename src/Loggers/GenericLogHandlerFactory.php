<?php

/**
 * TechDivision\Import\Loggers\ErrorLogHandlerFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers;

use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * Error Log Handler factory implementation.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericLogHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The log level to use.
     *
     * @var string
     */
    protected $defaultLogLevel;

    /**
     * The log level to use.
     *
     * @var string
     */
    protected $handlerClassName;

    /**
     * Initialize the processor with the actual configuration instance
     *
     * @param ConfigurationInterface $configuration    the actual configuration instance
     * @param string                 $handlerClassName Classname for monolog Logger handler
     */
    public function __construct(ConfigurationInterface $configuration, $handlerClassName)
    {
        $this->defaultLogLevel = $configuration->getLogLevel();
        $this->handlerClassName = $handlerClassName;
    }

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration
     *
     * @return \Monolog\Handler\HandlerInterface The handler instance
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {

        // load the params
        $params = $handlerConfiguration->getParams();

        // set the default log level, if not already set explicitly
        if (!isset($params[ConfigurationKeys::LEVEL])) {
            $params[ConfigurationKeys::LEVEL] = $this->defaultLogLevel;
        }

        // create and return the handler instance
        $reflectionClass = new \ReflectionClass($this->handlerClassName);
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $params));
    }
}
