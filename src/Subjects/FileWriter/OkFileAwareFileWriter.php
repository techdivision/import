<?php

/**
 * TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
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

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Handlers\OkFileHandlerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * File writer implementation for .OK files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileAwareFileWriter implements OkFileAwareFileWriterInterface
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    private $registryProcessor;

    /**
     * The actual source directory to load the files from.
     *
     * @var string
     */
    private $sourceDir;

    /**
     * The filesystem adapter instance.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    private $filesystemAdapter;

    /**
     * The .OK file handler instance to use.
     *
     * @var \TechDivision\Import\Handlers\OkFileHandlerInterface
     */
    private $handler;

    /**
     * The file resolver configuration instance.
     *
     * @var \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface
     */
    private $fileResolverConfiguration;

    /**
     * Initializes the file resolver with the application and the registry instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface the registry processor instance
     */
    protected function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the .OK file handler instance.
     *
     * @return \TechDivision\Import\Handlers\OkFileHandlerInterface The .OK file handler instance
     */
    protected function getHandler() : OkFileHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Return's the filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    protected function getFilesystemAdapter() : FilesystemAdapterInterface
    {
        return $this->filesystemAdapter;
    }

    /**
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    protected function getFileResolverConfiguration() : FileResolverConfigurationInterface
    {
        return $this->fileResolverConfiguration;
    }

    /**
     * Sets the actual source directory to load the files from.
     *
     * @param string $sourceDir The actual source directory
     *
     * @return void
     */
    protected function setSourceDir(string $sourceDir) : void
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Returns the actual source directory to load the files from.
     *
     * @return string The actual source directory
     */
    protected function getSourceDir() : string
    {
        return $this->sourceDir;
    }

    /**
     * Initializes the file resolver for the import process with the passed serial.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return void
     * @throws \Exception Is thrown if the configured source directory is not available
     */
    protected function initialize(string $serial) : void
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not the configured source directory is available
        if ($this->getFilesystemAdapter()->isDir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            // set the source directory, if it is accessible
            $this->setSourceDir($sourceDir);
            // return immediately
            return;
        }

        // throw an exception otherwise
        throw new \Exception(sprintf('Configured source directory "%s" is not available!', $sourceDir));
    }

    /**
     * Returns the suffix for the import files.
     *
     * @return string The suffix
     */
    protected function getSuffix() : string
    {
        return $this->getFileResolverConfiguration()->getSuffix();
    }

    /**
     * Set's he .OK file handler instance.
     *
     * @param \TechDivision\Import\Handlers\OkFileHandlerInterface $handler The .OK file handler instance
     *
     * @return void
     */
    public function setHandler(OkFileHandlerInterface $handler) :void
    {
        $this->handler = $handler;
    }

    /**
     * Set's the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter) : void
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * Set's the file resolver configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface $fileResolverConfiguration The file resolver configuration
     *
     * @return void
     */
    public function setFileResolverConfiguration(FileResolverConfigurationInterface $fileResolverConfiguration) : void
    {
        $this->fileResolverConfiguration = $fileResolverConfiguration;
    }

    /**
     * Create's the .OK files for the import with the passed serial.
     *
     * @param string $serial The serial to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $serial) : int
    {

        // initialize the resolver
        // @TODO Check if the method can not be moved to object initialization
        $this->initialize($serial);

        // initialize the array with the files matching the suffix found in the source directory
        return $this->getHandler()->createOkFiles(sprintf('%s/*.%s', $this->getSourceDir(), $this->getSuffix()));
    }
}
