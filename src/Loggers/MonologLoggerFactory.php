<?php

/**
 * TechDivision\Import\Loggers\MonologLoggerFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\LoggerConfigurationInterface;

/**
 * Monolog Logger factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The handler factory for the monolog logger factory.
     *
     * @var \TechDivision\Import\Loggers\MonologLoggerHandlerFactoryInterface
     */
    protected $monologLoggerHandlerFactory;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface         $container                   The DI container instance
     * @param \TechDivision\Import\Loggers\MonologLoggerHandlerFactoryInterface $monologLoggerHandlerFactory The Monolog Logger handler factory instance
     */
    public function __construct(
        ContainerInterface $container,
        MonologLoggerHandlerFactoryInterface $monologLoggerHandlerFactory
    ) {
        $this->container = $container;
        $this->monologLoggerHandlerFactory = $monologLoggerHandlerFactory;
    }

    /**
     * Return's the DI container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The DI container instance
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Return's the Monolog Logger handler factory.
     *
     * @return \TechDivision\Import\Loggers\MonologLoggerHandlerFactoryInterface The Monolog Logger handler factory instance
     */
    protected function getMonologLoggerHandlerFactory()
    {
        return $this->monologLoggerHandlerFactory;
    }

    /**
     * Creates a new logger instance based on the passed logger configuration.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface       $configuration       The system configuration
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
            $handlers[] = $this->getMonologLoggerHandlerFactory()->factory($handlerConfiguration);
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
