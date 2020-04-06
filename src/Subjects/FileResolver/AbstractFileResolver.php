<?php

/**
 * TechDivision\Import\Subjects\FileResolver\AbstractFileResolver
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

namespace TechDivision\Import\Subjects\FileResolver;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Loaders\PregMatchFilteredLoaderInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Abstract file resolver implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractFileResolver implements FileResolverInterface
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
     * The subject configuraiton instance.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * The filesystem adapter instance.
     *
     * @var \TechDivision\Import\Adapter\PhpFilesystemAdapterInterface
     */
    private $filesystemAdapter;

    /**
     * The filesystem loader instance.
     *
     * @var \TechDivision\Import\Loaders\PregMatchFilteredLoaderInterface
     */
    private $filesystemLoader;

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
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The processor instance
     */
    protected function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the filesystem loader instance.
     *
     * @return \TechDivision\Import\Loaders\PregMatchFilteredLoaderInterface The loader instance
     */
    protected function getFilesystemLoader() : PregMatchFilteredLoaderInterface
    {
        return $this->filesystemLoader;
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
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    protected function getFileResolverConfiguration() : FileResolverConfigurationInterface
    {
        return $this->getSubjectConfiguration()->getFileResolver();
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
     * Set's the filesystem loader used to resolve the file that has to be processed.
     *
     * @param \TechDivision\Import\Loaders\PregMatchFilteredLoaderInterface $loader The filesystem loader instance
     *
     * @return void
     */
    public function setLoader(PregMatchFilteredLoaderInterface $loader) : void
    {
        $this->filesystemLoader = $loader;
    }

    /**
     * Sets the subject configuration instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return void
     */
    public function setSubjectConfiguration(SubjectConfigurationInterface $subjectConfiguration) : void
    {
        $this->subjectConfiguration = $subjectConfiguration;
    }

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getSubjectConfiguration() : SubjectConfigurationInterface
    {
        return $this->subjectConfiguration;
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
     * Return's the filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function getFilesystemAdapter() : FilesystemAdapterInterface
    {
        return $this->filesystemAdapter;
    }

    /**
     * Return's the matches of all filters.
     *
     * @return array The array with the matches
     */
    public function getMatches()
    {
        return $this->getFilesystemLoader()->getMatches();
    }

    /**
     * Reset's the registered filters.
     *
     * @return void
     */
    public function reset() : void
    {
        $this->getFilesystemLoader()->reset();
    }

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     * @throws \Exception Is thrown, when the source directory is NOT available
     */
    public function loadFiles(string $serial) : array
    {

        // initialize the resolver
        // @TODO Check if the method can not be moved to object initialization
        $this->initialize($serial);

        // initialize the array with the files matching the suffix found in the source directory
        return $this->getFilesystemLoader()->load(sprintf('%s/*.%s', $this->getSourceDir(), $this->getSuffix()));
    }
}
