<?php

/**
 * TechDivision\Import\Loggers\SerialProcessor
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

use Monolog\Processor\ProcessorInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Serial processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SerialProcessor implements ProcessorInterface
{

    /**
     * The serial of the actual import process.
     *
     * @var string
     */
    protected $serial;

    /**
     * Initialize the processor with the serial of the actual import process.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The serial of the actual import process
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->serial = $configuration->getSerial();
    }

    /**
     * Will be invoked by the logger processor chain to append the serial.
     *
     * @param  array $record The record to append the serial to
     *
     * @return array The record with the appended serial
     */
    public function __invoke(array $record)
    {
        return array_merge($record, array('extra' => array('serial' => $this->serial)));
    }
}
