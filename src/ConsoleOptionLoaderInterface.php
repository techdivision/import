<?php

/**
 * TechDivision\Import\ConsoleOptionLoaderInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Interface for console option loader implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */
interface ConsoleOptionLoaderInterface
{

    /**
     * Load's the input options ans try's to initialize the configuration with the values found.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $instance The configuration instance to load the values for
     *
     * @return void
     */
    public function load(ConfigurationInterface $instance);
}
