<?php

/**
 * TechDivision\Import\Loggers\LoggerFactory
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

namespace TechDivision\Import\Loggers;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * The logger factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LoggerFactory implements LoggerFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The actual configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the factory with the the DI container instance and the actual configuration instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container     The DI container instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration with the data to create the loggers with
     */
    public function __construct(ContainerInterface $container, ConfigurationInterface $configuration)
    {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * Returns the actual configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
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
     * Create's and return's the loggers to use.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The array with the initialized loggers
     */
    public function createLoggers()
    {

        // load the configuration instance
        $configuration = $this->getConfiguration();

        // initialize the collection for the loggers
        $loggers = new ArrayCollection();

        // initialize the default system logger
        $systemLogger = new Logger('techdivision/import');
        $systemLogger->pushHandler(
            new ErrorLogHandler(
                ErrorLogHandler::OPERATING_SYSTEM,
                $configuration->getLogLevel()
            )
        );

        // add it to the array
        $loggers->set(LoggerKeys::SYSTEM, $systemLogger);

        // append the configured loggers or override the default one
        foreach ($configuration->getLoggers() as $name => $loggerConfiguration) {
            // load the factory class that creates the logger instance
            $loggerFactory = $this->getContainer()->get($loggerConfiguration->getId());
            // create the logger instance and add it to the available loggers
            $loggers->set($name, $loggerFactory->factory($configuration, $loggerConfiguration));
        }

        // return the collection with the initialized loggers
        return $loggers;
    }
}
