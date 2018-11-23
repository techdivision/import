<?php

/**
 * TechDivision\Import\Serializers\AdditionalAttributeCsvSerializerFactory
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Serializers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\CsvConfigurationInterface;
use TechDivision\Import\Utils\DependencyInjectionKeys;

/**
 * The factory implementation for CSV value serializer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeCsvSerializerFactory implements ConfigurationAwareSerializerFactoryInterface
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
     * Creates and returns the serializer instance.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The CSV configuration
     *
     * @return \TechDivision\Import\Serializers\ConfigurationAwareSerializerInterface The serializer instance
     */
    public function createSerializer(CsvConfigurationInterface $configuration)
    {

        // load the serializer instance from the container and pass the configuration
        /** @var \TechDivision\Import\Serializers\ConfigurationAwareSerializerInterface $serializer */
        $serializer = $this->container->get(DependencyInjectionKeys::IMPORT_SERIALIZER_CSV_ADDITIONAL_ATTRIBUTE);
        $serializer->init($configuration);

        // return the serializer instance
        return $serializer;
    }
}
