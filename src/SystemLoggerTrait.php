<?php

/**
 * TechDivision\Import\SystemLoggerTrait
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

namespace TechDivision\Import;

use TechDivision\Import\Utils\LoggerKeys;

/**
 * A trait that provides system logger handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait SystemLoggerTrait
{

    /**
     * The array with the system logger instances.
     *
     * @var array
     */
    protected $systemLoggers = array();

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    public function getSystemLogger($name = LoggerKeys::SYSTEM)
    {

        // query whether or not, the requested logger is available
        if (isset($this->systemLoggers[$name])) {
            return $this->systemLoggers[$name];
        }

        // throw an exception if the requested logger is NOT available
        throw new \Exception(sprintf('The requested logger \'%s\' is not available', $name));
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    public function getSystemLoggers()
    {
        return $this->systemLoggers;
    }
}
