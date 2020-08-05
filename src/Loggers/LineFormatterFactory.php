<?php

/**
 * TechDivision\Import\Loggers\LineFormatterFactory
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

use Monolog\Formatter\LineFormatter;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface;

/**
 * Line Formatter factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
