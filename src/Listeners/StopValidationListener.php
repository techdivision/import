<?php

/**
 * TechDivision\Import\Listeners\StopValidationListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Plugins\PluginInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * A listener implementation that stops the application on validation errors.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StopValidationListener extends AbstractListener
{

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface                      $event  The event that triggered the listener
     * @param \TechDivision\Import\Plugins\PluginInterface|null $plugin The plugin instance
     *
     * @return void
     */
    public function handle(EventInterface $event, PluginInterface $plugin = null)
    {

        // load the validations from the registry
        $validations = $this->getRegistryProcessor()->getAttribute(RegistryKeys::VALIDATIONS);

        // query whether or not we've validation errors
        if (is_array($validations) && sizeof($validations) > 0) {
            $plugin->getApplication()->stop('Stopped processing because of validation errors');
        }
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }
}
