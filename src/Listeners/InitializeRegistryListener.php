<?php

/**
 * TechDivision\Import\Listeners\InitializeRegistryListener
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

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * An listener implementation that initializes the registry.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class InitializeRegistryListener extends AbstractListener
{

    /**
     * The RegistryProcessor instance to handle running threads.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\ConfigurationInterface              $configuration     The configuration instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(ConfigurationInterface $configuration, RegistryProcessorInterface $registryProcessor)
    {
        $this->configuration = $configuration;
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface              $event       The event that triggered the listener
     * @param \TechDivision\Import\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {

        // initialize the status
        $status = array(
            RegistryKeys::STATUS                => 1,
            RegistryKeys::BUNCHES               => 0,
            RegistryKeys::SOURCE_DIRECTORY      => $this->getConfiguration()->getSourceDir(),
            RegistryKeys::MISSING_OPTION_VALUES => array()
        );

        // append it to the registry
        $this->getRegistryProcessor()->setAttribute($application->getSerial(), $status);
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Returns the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }
}
