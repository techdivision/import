<?php

/**
 * TechDivision\Import\Adapter\CsvImportAdapterFactory
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
use TechDivision\Import\Configuration\ImportAdapterAwareConfigurationInterface;
use TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface;

/**
 * Factory for all CSV import adapter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvImportAdapterFactory implements ImportAdapterFactoryInterface
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
     * Creates and returns the import adapter for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\ImportAdapterAwareConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The import adapter instance
     */
    public function createImportAdapter(ImportAdapterAwareConfigurationInterface $configuration)
    {

        // load the import adapter configuration
        $importAdapterConfiguration = $configuration->getImportAdapter();
        
        // Initialize adapter configuration with default values
        $this->initializeAdapterConfigurationWithDefaultValues($importAdapterConfiguration, $configuration);
        
        // load the serializer factory instance
        $serializerFactory = $this->container->get($importAdapterConfiguration->getSerializer()->getId());

        // create the instance and pass the import adapter configuration instance
        $importAdapter = $this->container->get(DependencyInjectionKeys::IMPORT_ADAPTER_IMPORT_CSV);
        $importAdapter->init($importAdapterConfiguration, $serializerFactory);

        // return the initialized import adapter instance
        return $importAdapter;
    }
    

    /**
     * @param ImportAdapterConfigurationInterface      $importAdapterConfiguration Import Adapter Configuration
     * @param ImportAdapterAwareConfigurationInterface $configuration              The subject configuration
     * @return void
     */
    protected function initializeAdapterConfigurationWithDefaultValues(
        ImportAdapterConfigurationInterface $importAdapterConfiguration,
        ImportAdapterAwareConfigurationInterface $configuration
    ) {
        // query whether or not a delimiter character has been configured
        if ($importAdapterConfiguration->getDelimiter() === null) {
            $importAdapterConfiguration->setDelimiter($configuration->getConfiguration()->getDelimiter());
        }

        // query whether or not a custom escape character has been configured
        if ($importAdapterConfiguration->getEscape() === null) {
            $importAdapterConfiguration->setEscape($configuration->getConfiguration()->getEscape());
        }

        // query whether or not a custom enclosure character has been configured
        if ($importAdapterConfiguration->getEnclosure() === null) {
            $importAdapterConfiguration->setEnclosure($configuration->getConfiguration()->getEnclosure());
        }

        // query whether or not a custom source charset has been configured
        if ($importAdapterConfiguration->getFromCharset() === null) {
            $importAdapterConfiguration->setFromCharset($configuration->getConfiguration()->getFromCharset());
        }

        // query whether or not a custom target charset has been configured
        if ($importAdapterConfiguration->getToCharset() === null) {
            $importAdapterConfiguration->setToCharset($configuration->getConfiguration()->getToCharset());
        }
    }
}
