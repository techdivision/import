<?php

/**
 * TechDivision\Import\Loaders\ProposedOkFilenameLoaderFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Loaders\Filters\DefaultOkFileFilter;
use TechDivision\Import\Loaders\Sorters\DefaultOkFileSorter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterFactoryInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Factory implementation for a loader for the proposed .OK filenames.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProposedOkFilenameLoaderFactory implements LoaderFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

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
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     */
    public function __construct(
        ContainerInterface $container,
        RegistryProcessorInterface $registryProcessor
    ) {
        $this->container = $container;
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
     * Create's and return's the apropriate loader instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface|null $subject The suject configuration instance
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    public function createLoader(SubjectConfigurationInterface $subject = null) : LoaderInterface
    {

        // load the filesystem adapter instance
        $filesystemAdapter = $this->getContainer()->get($subject->getFilesystemAdapter()->getId());

        // query whether or not we've a factory instance
        if ($filesystemAdapter instanceof FilesystemAdapterFactoryInterface) {
            $filesystemAdapter = $filesystemAdapter->createFilesystemAdapter($subject);
        }

        // initialize the chain with the loader instances
        $filesystemLoader = new FilesystemLoader($filesystemAdapter);
        $filteredLoader = new FilteredLoader($filesystemLoader);
        $pregMatchFilteredLoader = new PregMatchFilteredLoader($filteredLoader);
        $proposedOkFileLoader = new ProposedOkFilenameLoader($pregMatchFilteredLoader);
        $proposedOkFileLoader->addFilter(new DefaultOkFileFilter($subject));
        $proposedOkFileLoader->addSorter(new DefaultOkFileSorter());
        $proposedOkFileLoader->setSourceDir($this->getSourceDir($filesystemAdapter));
        $proposedOkFileLoader->setFileResolverConfiguration($subject->getFileResolver());

        // return the new loader instance
        return $proposedOkFileLoader;
    }
}
