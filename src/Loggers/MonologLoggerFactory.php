<?php

/**
 * TechDivision\Import\Loggers\MonologLoggerFactory
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

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\LoggerConfigurationInterface;

/**
 * Monolog Logger factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MonologLoggerFactory
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The DI container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the DI container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The DI container instance
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Creates a new logger instance based on the passed logger configuration.
     *
     * @param \TechDivision\Import\ConfigurationInterface                     $configuration       The system configuration
     * @param \TechDivision\Import\Configuration\LoggerConfigurationInterface $loggerConfiguration The logger configuration
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     */
    public function factory(
        ConfigurationInterface $configuration,
        LoggerConfigurationInterface $loggerConfiguration
    ) {

        // load the available processors from the configuration
        $availableProcessors = $loggerConfiguration->getProcessors();

        // initialize the processors
        $processors = array();
        /** @var \TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface $processorConfiguration */
        foreach ($availableProcessors as $processorConfiguration) {
            // create the processor (factory) instance
            $possibleProcessor = $this->getContainer()->get($processorConfiguration->getId());
            // query whether or not we've a factory or the instance
            if ($possibleProcessor instanceof ProcessorFactoryInterface) {
                $processors[] = $possibleProcessor->factory($processorConfiguration);
            } else {
                $processors[] = $possibleProcessor;
            }
        }

        // load the available handlers from the configuration
        $availableHandlers = $loggerConfiguration->getHandlers();

        // initialize the handlers
        $handlers = array();
        /** @var \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration */
        foreach ($availableHandlers as $handlerConfiguration) {
            // create the handler (factory) instance
            $possibleHandler = $this->getContainer()->get($handlerConfiguration->getId());
            // query whether or not we've a factory or the instance
            if ($possibleHandler instanceof HandlerFactoryInterface) {
                $handler = $possibleHandler->factory($handlerConfiguration);
            } else {
                $handler = $possibleHandler;
            }

            // if we've a formatter, initialize the formatter also
            if ($formatterConfiguration = $handlerConfiguration->getFormatter()) {
                // create the formatter (factory) instance
                $possibleFormatter = $this->getContainer()->get($formatterConfiguration->getId());
                // query whether or not we've a factory or the instance
                if ($possibleFormatter instanceof FormatterFactoryInterface) {
                    $handler->setFormatter($possibleFormatter->factory($formatterConfiguration));
                } else {
                    $handler->setFormatter($possibleFormatter);
                }
            }

            // add the handler
            $handlers[] = $handler;
        }

        // prepare the logger params
        $loggerParams = array(
            ConfigurationKeys::NAME       => $loggerConfiguration->getChannelName(),
            ConfigurationKeys::HANDLERS   => $handlers,
            ConfigurationKeys::PROCESSORS => $processors
        );

        // append the params from the logger configuration
        $loggerParams = array_merge($loggerParams, $loggerConfiguration->getParams());

        // initialize the Monolog Logger instance itself
        $reflectionClass = new \ReflectionClass(Logger::class);
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $loggerParams));
    }
}
