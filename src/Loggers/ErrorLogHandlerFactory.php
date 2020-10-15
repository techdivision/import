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
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers;

use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * Error Log Handler factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 *
 * @deprecated use GenericLogHandlerFactory
 */
class ErrorLogHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The log level to use.
     *
     * @var string
     */
    protected $defaultLogLevel;

    /**
     * Initialize the processor with the actual configuration instance
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The actual configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->defaultLogLevel = $configuration->getLogLevel();
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
        $reflectionClass = new \ReflectionClass(ErrorLogHandler::class);
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $params));
    }
}
