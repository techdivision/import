<?php

/**
 * TechDivision\Import\Subjects\FileWriter\FileWriterFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileWriter;

use TechDivision\Import\Handlers\HandlerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterFactoryInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Generic factory implementation for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FileWriterFactory implements FileWriterFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * The .OK file handler factory instance
     *
     * @var \TechDivision\Import\Handlers\HandlerFactoryInterface
     */
    private $handlerFactory;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container      The DI container instance
     * @param \TechDivision\Import\Loaders\LoaderFactoryInterface       $handlerFactory The .OK file handler factory instance
     */
    public function __construct(
        ContainerInterface $container,
        HandlerFactoryInterface $handlerFactory
    ) {
        $this->container = $container;
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * Return's the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The container instance
     */
    protected function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    /**
     * Return's the .OK file handler factory instance.
     *
     * @return  \TechDivision\Import\Handlers\HandlerFactoryInterface The .OK file handler factory instance
     */
    protected function getHandlerFactory() : HandlerFactoryInterface
    {
        return $this->handlerFactory;
    }

    /**
     * Creates and returns the file resolver instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the file resolver for
     *
     * @return \TechDivision\Import\Subjects\FileWriter\FileWriterInterface The file resolver instance
     */
    public function createFileWriter(SubjectConfigurationInterface $subject) : FileWriterInterface
    {

        // load the DI container instance
        $container = $this->getContainer();

        // load the proposed .OK file loader
        $handler = $this->getHandlerFactory()->createHandler($subject);

        // load the filesystem adapter instance
        /** \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter */
        $filesystemAdapter = $container->get($subject->getFilesystemAdapter()->getId());

        // query whether or not we've a factory instance
        if ($filesystemAdapter instanceof FilesystemAdapterFactoryInterface) {
            $filesystemAdapter = $filesystemAdapter->createFilesystemAdapter($subject);
        }

        // create a new file writer instance for the subject with the passed configuration
        /** @var \TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriterInterface $fileWriter */
        $fileWriter = $container->get($subject->getFileWriter()->getId());
        $fileWriter->setHandler($handler);
        $fileWriter->setFilesystemAdapter($filesystemAdapter);
        $fileWriter->setFileResolverConfiguration($subject->getFileResolver());

        // return the file writer instance
        return $fileWriter;
    }
}
