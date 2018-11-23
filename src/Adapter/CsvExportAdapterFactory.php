<?php

/**
 * TechDivision\Import\Adapter\CsvExportAdapterFactory
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

namespace TechDivision\Import\Adapter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Utils\DependencyInjectionKeys;

/**
 * Factory for all CSV export adapter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The export adapter instance
     */
    public function createExportAdapter(SubjectConfigurationInterface $subjectConfiguration)
    {

        // load the export adapter configuration
        $exportAdapterConfiguration = $subjectConfiguration->getExportAdapter();

        // load the serializer factory instance
        $serializerFactory = $this->container->get($exportAdapterConfiguration->getSerializer()->getId());

        // create the instance and pass the export adapter configuration instance
        $exportAdapter = $this->container->get(DependencyInjectionKeys::IMPORT_ADAPTER_EXPORT_CSV);
        $exportAdapter->init($exportAdapterConfiguration, $serializerFactory);

        // return the initialized export adapter instance
        return $exportAdapter;
    }
}
