<?php

/**
 * TechDivision\Import\Loggers\MonologLoggerHandlerFormatterFactory
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

use TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface;

/**
 * Interface for Monolog Logger formatter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface MonologLoggerFormatterFactoryInterface
{

    /**
     * Creates a new logger handler formatter instance based on the passed formatter configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $formatterConfiguration The formatter configuration
     *
     * @return \Monolog\Formatter\FormatterInterface The logger handler formatter instance
     */
    public function factory(FormatterConfigurationInterface $formatterConfiguration);
}
