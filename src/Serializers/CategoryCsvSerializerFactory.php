<?php

/**
 * TechDivision\Import\Serializers\CategoryCsvSerializerFactory.php
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Serializers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Utils\DependencyInjectionKeys;
use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Serializer\Configuration\SerializerConfigurationInterface;

/**
 * The factory implementation for category CSV serializer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryCsvSerializerFactory implements SerializerFactoryInterface
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
     * @param \TechDivision\Import\Serializer\Configuration\SerializerConfigurationInterface $serializerConfiguration The serializer configuration
     *
     * @return \TechDivision\Import\Serializer\ConfigurationAwareSerializerInterface The serializer instance
     */
    public function createSerializer(SerializerConfigurationInterface $serializerConfiguration)
    {

        // load the serializer instance from the container and pass the configuration
        /** @var \TechDivision\Import\Serializer\SerializerInterface $serializer */
        $serializer = $this->container->get(DependencyInjectionKeys::IMPORT_SERIALIZER_CSV_CATEGORY);
        $serializer->init($serializerConfiguration);

        // return the serializer instance
        return $serializer;
    }
}
