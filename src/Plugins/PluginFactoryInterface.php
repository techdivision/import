<?php

/**
 * TechDivision\Import\Plugins\PluginFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * The interface for all plugin factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PluginFactoryInterface
{

    /**
     * Factory method to create new plugin instance.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration
     *
     * @return \TechDivision\Import\Plugins\PluginInterface The plugin instance
     */
    public function createPlugin(PluginConfigurationInterface $pluginConfiguration);
}
