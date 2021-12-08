<?php

/**
 * TechDivision\Import\Loggers\FormatterFactoryInterface
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
 * Interface for formatter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FormatterFactoryInterface
{

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface $formatterConfiguration The formatter configuration
     *
     * @return object The formatter instance
     */
    public function factory(FormatterConfigurationInterface $formatterConfiguration);
}
