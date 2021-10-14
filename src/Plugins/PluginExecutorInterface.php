<?php

/**
 * TechDivision\Import\Plugins\PluginExecutor
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * The  interface for all plugin executor instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PluginExecutorInterface
{

    /**
     * Executes the plugin with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $plugin The message with the plugin information
     *
     * @return void
     */
    public function execute(PluginConfigurationInterface $plugin);
}
