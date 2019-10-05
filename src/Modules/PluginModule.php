<?php

/**
 * TechDivision\Import\Modules\PluginModule
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

namespace TechDivision\Import\Modules;

use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\ConfigurationManagerInterface;
use TechDivision\Import\Plugins\PluginExecutorInterface;

/**
 * A module implementation that provides plug-in functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PluginModule implements ModuleInterface
{

    /**
     * The application instance.
     *
     * @var \TechDivision\Import\ApplicationInterface
     */
    protected $application;

    /**
     * The plugin executor instance.
     *
     * @var \TechDivision\Import\Plugins\PluginExecutorInterface
     */
    protected $pluginExecutor;

    /**
     * The configuration manager instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configurationManager;

    /**
     * Initializes the module with the application, configuration and plug-in executor instance.
     *
     * @param \TechDivision\Import\ApplicationInterface            $application          The application instance
     * @param \TechDivision\Import\ConfigurationManagerInterface   $configurationManager The configuration manager instance
     * @param \TechDivision\Import\Plugins\PluginExecutorInterface $pluginExecutor       The plug-in executor instance
     */
    public function __construct(
        ApplicationInterface $application,
        ConfigurationManagerInterface $configurationManager,
        PluginExecutorInterface $pluginExecutor
    ) {

        // initialize the application, the configuration and the plugin executor
        $this->application = $application;
        $this->configurationManager = $configurationManager;
        $this->pluginExecutor = $pluginExecutor;
    }

    /**
     * Return's the application instance.
     *
     * @return \TechDivision\Import\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Return's the configuration manager instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration manager instance
     */
    protected function getConfigurationManager()
    {
        return $this->configurationManager;
    }

    /**
     * Return's the plug-in executor instance.
     *
     * @return \TechDivision\Import\Plugins\PluginExecutorInterface The plug-in executor instance
     */
    protected function getPluginExecutor()
    {
        return $this->pluginExecutor;
    }

    /**
     * Inovkes the plug-in functionality.
     *
     * @return void
     */
    public function process()
    {

        // process the plugins defined in the configuration
        /** @var \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration */
        foreach ($this->getConfigurationManager()->getPlugins() as $pluginConfiguration) {
            // query whether or not the operation has been stopped
            if ($this->getApplication()->isStopped()) {
                break;
            }

            // execute the plugin instance
            $this->getPluginExecutor()->execute($pluginConfiguration);
         }
    }
}
