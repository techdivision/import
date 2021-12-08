<?php

/**
 * TechDivision\Import\Plugins\PluginInterface
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
use TechDivision\Import\Adapter\ImportAdapterInterface;

/**
 * The interface for all plugins.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PluginInterface
{

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process();

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial();

    /**
     * Return's the application instance.
     *
     * @return \TechDivision\Import\ApplicationInterface The application instance
     */
    public function getApplication();

    /**
     *  Set's the plugin configuration instance.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration instance
     *
     * @return void
     */
    public function setPluginConfiguration(PluginConfigurationInterface $pluginConfiguration);

    /**
     * Return's the plugin configuration instance.
     *
     * @return \TechDivision\Import\Configuration\PluginConfigurationInterface The plugin configuration instance
     */
    public function getPluginConfiguration();

    /**
     * Set's the import adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ImportAdapterInterface $importAdapter The import adapter instance
     *
     * @return void
     */
    public function setImportAdapter(ImportAdapterInterface $importAdapter);

    /**
     * Return's the import adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ImportAdapterInterface The import adapter instance
     */
    public function getImportAdapter();

    /**
     * Return's the plugin's execution context configuration.
     *
     * @return \TechDivision\Import\Configuration\ExecutionContextInterface The execution context configuration to use
     */
    public function getExecutionContext();
}
