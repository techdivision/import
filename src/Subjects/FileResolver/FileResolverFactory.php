<?php

/**
 * TechDivision\Import\Subjects\FileResolver\FileResolverFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileResolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterFactoryInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Handlers\HandlerFactoryInterface;
use TechDivision\Import\Loaders\FilteredLoader;
use TechDivision\Import\Loaders\PregMatchFilteredLoader;
use TechDivision\Import\Loaders\FilesystemLoader;
use TechDivision\Import\Loaders\Filters\OkFileFilter;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Factory for file resolver instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FileResolverFactory implements FileResolverFactoryInterface
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
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    private $registryProcessor;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container         The DI container instance
     * @param \TechDivision\Import\Handlers\HandlerFactoryInterface     $handlerFactory    The handler factory instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     */
    public function __construct(
        ContainerInterface $container,
        HandlerFactoryInterface $handlerFactory,
        RegistryProcessorInterface $registryProcessor
    ) {
        $this->container = $container;
        $this->handlerFactory = $handlerFactory;
        $this->registryProcessor = $registryProcessor;
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
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    protected function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
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
     * Return's the actual source directory.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter to validate the source directory with
     *
     * @return string The actual source directory
     * @throws \Exception Is thrown, if the actual source directory can not be loaded
     */
    protected function getSourceDir(FilesystemAdapterInterface $filesystemAdapter) : string
    {

        // try to load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not the configured source directory is available
        if (is_array($status) && $filesystemAdapter->isDir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            return $sourceDir;
        }

        // throw an exception if the the actual source directory can not be loaded
        throw new \Exception(sprintf('Can\'t load source directory to create file writer instance for'));
    }

    /**
     * Creates and returns the file resolver instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the file resolver for
     *
     * @return \TechDivision\Import\Subjects\FileResolver\FileResolverInterface The file resolver instance
     */
    public function createFileResolver(SubjectConfigurationInterface $subject) : FileResolverInterface
    {

        // load the DI container instance
        $container = $this->getContainer();

        // load the proposed .OK file loader
        $handler = $this->getHandlerFactory()->createHandler($subject);

        // load the filesystem adapter instance
        $filesystemAdapter = $container->get($subject->getFilesystemAdapter()->getId());

        // query whether or not we've a factory instance
        /** \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter */
        if ($filesystemAdapter instanceof FilesystemAdapterFactoryInterface) {
            $filesystemAdapter = $filesystemAdapter->createFilesystemAdapter($subject);
        }

        // initialize the filter for the files that has to be imported
        // AND matches the .OK file, if one has been requested
        $filter = new OkFileFilter($handler, $subject, $this->getSourceDir($filesystemAdapter));

        // initialize the loader for the files that has to be imported
        $filesystemLoader = new FilesystemLoader($filesystemAdapter);
        $filteredLoader = new FilteredLoader($filesystemLoader);
        $pregMatchFilteredloader = new PregMatchFilteredLoader($filteredLoader);
        $pregMatchFilteredloader->addFilter($filter);

        // create a new file resolver instance for the subject with the passed configuration
        /** @var \TechDivision\Import\Subjects\FileResolver\FileResolverInterface $fileResolver */
        $fileResolver = $container->get($subject->getFileResolver()->getId());
        $fileResolver->setFilesystemAdapter($filesystemAdapter);
        $fileResolver->setSubjectConfiguration($subject);
        $fileResolver->setLoader($pregMatchFilteredloader);

        // return the file resolver instance
        return $fileResolver;
    }
}
