<?php

/**
 * TechDivision\Import\Loggers\ProcessorFactoryInterface
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

use TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface;

/**
 * Interface for processor factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ProcessorFactoryInterface
{

    /**
     * Creates a new processor instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface $processorConfiguration The processor configuration
     *
     * @return object The processor instance
     */
    public function factory(ProcessorConfigurationInterface $processorConfiguration);
}
