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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Events;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

/**
 * A factory implementation to create a new event emitter instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EmitterFactory implements EmitterFactoryInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The DI container builder instance.
     *
     * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
     */
    protected $container;

    /**
     * The constructor to initialize the instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface                     $configuration The configuration instance
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface $container     The container instance
     */
    public function __construct(ConfigurationInterface $configuration, TaggedContainerInterface $container)
    {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * The factory method that creates a new emitter instance.
     *
     * @return void
     */
    public function createEmitter()
    {

        // initialize the event emitter
        $emitter = new Emitter();

        // load the listener configuration from the configuration
        $availableListeners = $this->configuration->getListeners();

        // load, initialize and add the configured listeners to the emitter
        foreach ($availableListeners as $listeners) {
            $this->prepareListeners($emitter, $listeners);
        }

        // load the available operations from the configuration
        $availableOperations = $this->configuration->getOperations();

        // load, initialize and add the configured listeners for the actual operation
        /** @var \TechDivision\Import\Configuration\OperationConfigurationInterface $operation */
        foreach ($availableOperations as $operation) {
            if ($operation->equals($this->configuration->getOperation())) {
                // load the operation's listeners
                $operationListeners = $operation->getListeners();
                // prepare the operation's listeners
                foreach ($operationListeners as $listeners) {
                    $this->prepareListeners($emitter, $listeners);
                }
            }
        }

        // return the initialized emitter instance
        return $emitter;
    }

    /**
     * Prepare the listeners defined in the system configuration.
     *
     * @param \League\Event\EmitterInterface $emitter   The event emitter to prepare the listeners for
     * @param array                          $listeners The array with the listeners
     * @param string                         $eventName The actual event name
     *
     * @return void
     */
    protected function prepareListeners(EmitterInterface $emitter, array $listeners, $eventName = null)
    {

        // iterate over the array with listeners and prepare them
        foreach ($listeners as $key => $listener) {
            // we have to initialize the event name only on the first level
            if ($eventName == null) {
                $eventName = $key;
            }
            // query whether or not we've an subarray or not
            if (is_array($listener)) {
                $this->prepareListeners($emitter, $listener, $eventName);
            } else {
                $emitter->addListener($eventName, $this->container->get($listener));
            }
        }
    }
}
