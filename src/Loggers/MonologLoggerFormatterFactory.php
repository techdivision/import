<?php

/**
 * TechDivision\Import\Loggers\MonologLoggerFormatterFactory
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
use TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface;

/**
 * Monolog Logger formatter factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MonologLoggerFormatterFactory implements MonologLoggerFormatterFactoryInterface
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
     * Creates a new logger handler formatter instance based on the passed formatter configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $formatterConfiguration The formatter configuration
     *
     * @return \Monolog\Formatter\FormatterInterface The logger handler formatter instance
     */
    public function factory(FormatterConfigurationInterface $formatterConfiguration)
    {

        // create the formatter (factory) instance
        $possibleFormatter = $this->getContainer()->get($formatterConfiguration->getId());
        // query whether or not we've a factory or the instance
        if ($possibleFormatter instanceof FormatterFactoryInterface) {
            return $possibleFormatter->factory($formatterConfiguration);
        }

        // return the formatter instance
        return $possibleFormatter;
    }
}
