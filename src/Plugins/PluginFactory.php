<?php

/**
 * TechDivision\Import\Plugins\PluginFactory
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * A generic plugin factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PluginFactory implements PluginFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The DI container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Factory method to create new plugin instance.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration
     *
     * @return \TechDivision\Import\Plugins\PluginInterface The plugin instance
     */
    public function createPlugin(PluginConfigurationInterface $pluginConfiguration)
    {

        // load the plugin instance from the DI container and set the plugin configuration
        $pluginInstance = $this->container->get($pluginConfiguration->getId());
        $pluginInstance->setPluginConfiguration($pluginConfiguration);

        // return the plugin instance
        return $pluginInstance;
    }
}
