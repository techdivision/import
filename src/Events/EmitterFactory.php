<?php

/**
 * TechDivision\Import\Events\EmitterFactory
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

namespace TechDivision\Import\Events;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use TechDivision\Import\ConfigurationManagerInterface;
use TechDivision\Import\Configuration\ListenerAwareConfigurationInterface;

/**
 * A factory implementation to create a new event emitter instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EmitterFactory implements EmitterFactoryInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * The DI container builder instance.
     *
     * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
     */
    protected $container;

    /**
     * The array with the event name => DI ID mapping
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * The constructor to initialize the instance.
     *
     * @param \TechDivision\Import\ConfigurationManagerInterface              $configurationManager The configuration instance
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface $container            The container instance
     */
    public function __construct(ConfigurationManagerInterface $configurationManager, TaggedContainerInterface $container)
    {
        $this->container = $container;
        $this->configurationManager = $configurationManager;
    }

    /**
     * The factory method that creates a new emitter instance.
     *
     * @return void
     */
    public function createEmitter()
    {

        // load the listener configuration from the configuration
        $this->loadListeners($this->configurationManager->getConfiguration());

        // load the operations that has to be executed from the configuration
        $operations = $this->configurationManager->getOperations();

        // load, initialize and add the configured listeners for the actual operation
        /** @var \TechDivision\Import\Configuration\OperationConfigurationInterface $operation */
        foreach ($operations as $operation) {
            // load the operation's listeners
            $this->loadListeners($operation);
            // load the operation's registered plugins
            /** @var \TechDivision\Import\Configuration\PluginConfigurationInterface $plugin */
            foreach ($operation->getPlugins() as $plugin) {
                // load the plugin listeners
                $this->loadListeners($plugin, sprintf('%s.%s', $operationName = $operation->getName(), $pluginName = $plugin->getName()));
                // load the plugin's registered subjects
                /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
                foreach ($plugin->getSubjects() as $subject) {
                    // load the subject listeners
                    $this->loadListeners($subject, sprintf('%s.%s.%s', $operationName, $pluginName, $subject->getName()));
                }
            }
        }

        // initialize the event emitter, register the listeners
        return $this->registerListener(new Emitter());
    }

    /**
     * Loads the listener configuration for the passed configuration instance.
     *
     * @param \TechDivision\Import\Configuration\ListenerAwareConfigurationInterface $configuration The configuration with the listener definition
     * @param string                                                                 $parentName    The parent configuration name
     *
     * @return void
     */
    protected function loadListeners(ListenerAwareConfigurationInterface $configuration, $parentName = null)
    {

        // load the listener configurations
        $listenerConfigurations = $configuration->getListeners();

        // prepare the listeners with the even names as key and the DI ID as value
        foreach ($listenerConfigurations as $listeners) {
            foreach ($listeners as $key => $name) {
                $this->listeners[$parentName == null ? $key : sprintf('%s.%s', $parentName, $key)] = $name;
            }
        }
    }

    /**
     * Registers the listeners defined in the system configuration.
     *
     * @param \League\Event\EmitterInterface $emitter The event emitter to prepare the listeners for
     *
     * @return \League\Event\EmitterInterface $emitter The initialized event emitter instance
     */
    protected function registerListener(EmitterInterface $emitter)
    {

        // iterate over the found listeners and add instances to the emitter
        foreach ($this->listeners as $eventName => $listeners) {
            foreach ($listeners as $id) {
                $emitter->addListener($eventName, $this->container->get($id));
            }
        }

        // return the emitter instance
        return $emitter;
    }
}
