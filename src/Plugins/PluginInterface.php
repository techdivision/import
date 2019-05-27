<?php

/**
 * TechDivision\Import\Plugins\PluginInterface
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * The interface for all plugins.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PluginInterface
{

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial();

    /**
     * Return's the plugin configuration instance.
     *
     * @return \TechDivision\Import\Configuration\PluginConfigurationInterface The plugin configuration instance
     */
    public function getPluginConfiguration();

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process();

    /**
     *  Set's the plugin configuration instance.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration instance
     *
     * @return void
     */
    public function setPluginConfiguration(PluginConfigurationInterface $pluginConfiguration);
}
