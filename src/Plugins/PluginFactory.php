<?php

/**
 * TechDivision\Import\Plugins\PluginFactory
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Adapter\ImportAdapterInterface;
use TechDivision\Import\Adapter\ImportAdapterFactoryInterface;
use TechDivision\Import\Adapter\ExportAdapterInterface;
use TechDivision\Import\Adapter\ExportAdapterFactoryInterface;

/**
 * A generic plugin factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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

        // load the import adapter instance from the DI container and set it on the plugin instance
        $importAdapter = $this->container->get($pluginConfiguration->getImportAdapter()->getId());

        // query whether or not we've found a factory
        if ($importAdapter instanceof ImportAdapterFactoryInterface) {
            $pluginInstance->setImportAdapter($importAdapter->createImportAdapter($pluginConfiguration));
        } else {
            throw new \Exception(
                sprintf(
                    'Expected either an instance of ImportAdapterInterface or ImportAdapterFactoryInterface for DI ID "%s"',
                    $pluginConfiguration->getImportAdapter()->getId()
                )
            );
        }

        // query whether or not we've a plugin instance that implements the exportable plugin interface
        if ($pluginInstance instanceof ExportablePluginInterface) {
            // load the export adapter instance from the DI container and set it on the plugin instance
            $exportAdapter = $this->container->get($pluginConfiguration->getExportAdapter()->getId());

            // query whether or not we've found a factory
            if ($exportAdapter instanceof ExportAdapterFactoryInterface) {
                $pluginInstance->setExportAdapter($exportAdapter->createExportAdapter($pluginConfiguration));
            } else {
                throw new \Exception(
                    sprintf(
                        'Expected either an instance of ExportAdapterInterface or ExportAdapterFactoryInterface for DI ID "%s"',
                        $pluginConfiguration->getExportAdapter()->getId()
                    )
                );
            }
        }

        // return the plugin instance
        return $pluginInstance;
    }
}
