<?php

/**
 * TechDivision\Import\Loggers\MonologLoggerHandlerFactory
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * Monolog Logger handler factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MonologLoggerHandlerFactory implements MonologLoggerHandlerFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The Monolog Logger formatter factory instance.
     *
     * @var \TechDivision\Import\Loggers\MonologLoggerFormatterFactoryInterface
     */
    protected $monologLoggerFormatterFactory;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface           $container                     The DI container instance
     * @param \TechDivision\Import\Loggers\MonologLoggerFormatterFactoryInterface $monologLoggerFormatterFactory The Monolog Logger formatter factory instance
     */
    public function __construct(ContainerInterface $container, MonologLoggerFormatterFactoryInterface $monologLoggerFormatterFactory)
    {
        $this->container = $container;
        $this->monologLoggerFormatterFactory = $monologLoggerFormatterFactory;
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
     * Return's the Monolog Logger formatter factory instance.
     *
     * @return \TechDivision\Import\Loggers\MonologLoggerFormatterFactoryInterface The Monolog Logger formatter factory instance
     */
    protected function getMonologLoggerFormatterFactory()
    {
        return $this->monologLoggerFormatterFactory;
    }

    /**
     * Creates a new logger handler instance based on the passed handler configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration
     *
     * @return \Monolog\Handler\HandlerInterface The logger handler instance
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {

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
            $handler->setFormatter($this->getMonologLoggerFormatterFactory()->factory($formatterConfiguration));
        }

        // return the handler
        return $handler;
    }
}
