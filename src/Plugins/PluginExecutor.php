<?php

/**
 * TechDivision\Import\Plugins\PluginExecutor
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

namespace TechDivision\Import\Plugins;

use League\Event\EmitterInterface;
use TechDivision\Import\Utils\EventNames;
use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * The plug-in executor instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PluginExecutor implements PluginExecutorInterface
{

    /**
     * The plug-in factory instance.
     *
     * @var \TechDivision\Import\Plugins\PluginFactoryInterface
     */
    protected $pluginFactory;

    /**
     * The event emitter instance.
     *
     * @var \League\Event\EmitterInterface
     */
    protected $emitter;

    /**
     * Initializes the plug-in executor with the application instance.
     *
     * @param \TechDivision\Import\Plugins\PluginFactoryInterface $pluginFactory  The plug-in factory instance
     * @param \League\Event\EmitterInterface                      $emitter        The event emitter instance
     */
    public function __construct(
        PluginFactoryInterface $pluginFactory,
        EmitterInterface $emitter
    ) {

        // initialize the subject factory and the event emitter
        $this->pluginFactory = $pluginFactory;
        $this->emitter = $emitter;
    }

    /**
     * Return's the event emitter instance.
     *
     * @return \League\Event\EmitterInterface The event emitter instance
     */
    protected function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * Return's the plug-in factory instance.
     *
     * @return \TechDivision\Import\Plugins\PluginFactoryInterface The plug-in factory instance
     */
    protected function getPluginFactory()
    {
        return $this->pluginFactory;
    }

    /**
     * Executes the plugin with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $plugin The message with the plugin information
     *
     * @return void
     */
    public function execute(PluginConfigurationInterface $plugin)
    {

        // initialize the plugin
        $pluginInstance = $this->getPluginFactory()->createPlugin($plugin);

        try {
            // load the subject + plug-in ID to prepare the event names
            $pluginName = $plugin->getName();

            // invoke the event that has to be fired before the plugin will be executed
            $this->getEmitter()->emit(EventNames::PLUGIN_PROCESS_START, $pluginInstance);
            $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_PROCESS_START), $pluginInstance);

            // initialize the counter
            $counter = 1;

            // process the plugin
            $pluginInstance->process();

            // query whether or not, we've to export artefacts
            if ($pluginInstance instanceof ExportablePluginInterface) {
                try {
                    // invoke the event that has to be fired before the subject's export method will be invoked
                    $this->getEmitter()->emit(EventNames::PLUGIN_EXPORT_START, $pluginInstance);
                    $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_EXPORT_START), $pluginInstance);

                    // export the plugin's artefacts if available
                    $pluginInstance->export(date('Ymd-His'), str_pad($counter++, 2, 0, STR_PAD_LEFT));

                    // invoke the event that has to be fired after the subject's export method has been invoked
                    $this->getEmitter()->emit(EventNames::PLUGIN_EXPORT_SUCCESS, $pluginInstance);
                    $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_EXPORT_SUCCESS), $pluginInstance);
                } catch (\Exception $e) {
                    // invoke the event that has to be fired when the subject's export method throws an exception
                    $this->getEmitter()->emit(EventNames::PLUGIN_EXPORT_FAILURE, $pluginInstance);
                    $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_EXPORT_FAILURE), $pluginInstance);

                    // re-throw the exception
                    throw $e;
                }
            }

            // invoke the event that has to be fired after the plugin has been executed
            $this->getEmitter()->emit(EventNames::PLUGIN_PROCESS_SUCCESS, $pluginInstance);
            $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_PROCESS_SUCCESS), $pluginInstance);
        } catch (\Exception $e) {
            // invoke the event that has to be fired when the plugin throws an exception
            $this->getEmitter()->emit(EventNames::PLUGIN_PROCESS_FAILURE, $pluginInstance);
            $this->getEmitter()->emit(sprintf('%s.%s', $pluginName, EventNames::PLUGIN_PROCESS_FAILURE), $pluginInstance);

            // re-throw the exception
            throw $e;
        }
    }
}
