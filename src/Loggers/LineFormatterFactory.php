<?php

/**
 * TechDivision\Import\Loggers\LineFormatterFactory
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

use Monolog\Formatter\LineFormatter;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface;

/**
 * Line Formatter factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LineFormatterFactory implements FormatterFactoryInterface
{

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface $formatterConfiguration The formatter configuration
     *
     * @return object The formatter instance
     */
    public function factory(FormatterConfigurationInterface $formatterConfiguration)
    {
        $reflectionClass = new \ReflectionClass(LineFormatter::class);
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $formatterConfiguration->getParams()));
    }
}
