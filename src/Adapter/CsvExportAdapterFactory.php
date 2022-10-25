<?php

/**
 * TechDivision\Import\Adapter\CsvExportAdapterFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter;

use TechDivision\Import\Utils\DependencyInjectionKeys;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\ExportAdapterAwareConfigurationInterface;
use TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface;

/**
 * Factory for all CSV export adapter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvExportAdapterFactory implements ExportAdapterFactoryInterface
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
     * Creates and returns the export adapter for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\ExportAdapterAwareConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The export adapter instance
     */
    public function createExportAdapter(ExportAdapterAwareConfigurationInterface $configuration)
    {

        // load the export adapter configuration
        $exportAdapterConfiguration = $configuration->getExportAdapter();

        // Initialize adapter configuration with default values
        $this->initializeAdapterConfigurationWithDefaultValues($exportAdapterConfiguration, $configuration);

        // load the serializer factory instance
        $serializerFactory = $this->container->get($exportAdapterConfiguration->getSerializer()->getId());

        // create the instance and pass the export adapter configuration instance
        $exportAdapter = $this->container->get(DependencyInjectionKeys::IMPORT_ADAPTER_EXPORT_CSV);
        $exportAdapter->init($exportAdapterConfiguration, $serializerFactory);

        // return the initialized export adapter instance
        return $exportAdapter;
    }


    /**
     * @param ExportAdapterConfigurationInterface      $exportAdapterConfiguration Export Adapter Configuration
     * @param ImportAdapterAwareConfigurationInterface $configuration              The subject configuration
     * @return void
     */
    protected function initializeAdapterConfigurationWithDefaultValues(
        ExportAdapterConfigurationInterface $exportAdapterConfiguration,
        ExportAdapterAwareConfigurationInterface $configuration
    ) {
        // query whether or not a delimiter character has been configured
        if ($exportAdapterConfiguration->getDelimiter() === null) {
            $exportAdapterConfiguration->setDelimiter($configuration->getConfiguration()->getDelimiter());
        }

        // query whether or not a custom escape character has been configured
        if ($exportAdapterConfiguration->getEscape() === null) {
            $exportAdapterConfiguration->setEscape($configuration->getConfiguration()->getEscape());
        }

        // query whether or not a custom enclosure character has been configured
        if ($exportAdapterConfiguration->getEnclosure() === null) {
            $exportAdapterConfiguration->setEnclosure($configuration->getConfiguration()->getEnclosure());
        }
    }
}
