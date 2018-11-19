<?php

/**
 * TechDivision\Import\Plugins\FileResolver\AbstractFileResolver
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

namespace TechDivision\Import\Plugins\FileResolver;

use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

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
     * The appliation instance.
     *
     * @var \TechDivision\Import\ApplicationInterface
     */
    private $application;

    /**
     * The actual source directory to load the files from.
     *
     * @var string
     */
    private $sourceDir;

    /**
     * The OK file suffix to use.
     *
     * @var string
     */
    private $okFileSuffix = 'ok';

    /**
     * The subject configuraiton instance.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * Initializes the file resolver with the application and the registry instance.
     *
     * @param \TechDivision\Import\ApplicationInterface                $application       The application instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry instance
     */
    public function __construct(ApplicationInterface $application, RegistryProcessorInterface $registryProcessor)
    {
        $this->application = $application;
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Returns the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the application instance.
     *
     * @return \TechDivision\Import\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Sets the actual source directory to load the files from.
     *
     * @param string $sourceDir The actual source directory
     */
    protected function setSourceDir($sourceDir)
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Returns the actual source directory to load the files from.
     *
     * @return string The actual source directory
     */
    protected function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    protected function getFileResolverConfiguration()
    {
        return $this->getSubjectConfiguration()->getFileResolver();
    }

    /**
     * Returns the delement separator char.
     *
     *  @return string The element separator char
     */
    protected function getElementSeparator()
    {
        return $this->getFileResolverConfiguration()->getElementSeparator();
    }

    /**
     * Returns the elements the filenames consists of.
     *
     * @return array The array with the filename elements
     */
    protected function getPatternElements()
    {
        return $this->getFileResolverConfiguration()->getPatternElements();
    }

    /**
     * Returns the suffix for the import files.
     *
     * @return string The suffix
     */
    protected function getSuffix()
    {
        return $this->getFileResolverConfiguration()->getSuffix();
    }

    /**
     * Returns the OK file suffix to use.
     *
     * @return string The OK file suffix
     */
    protected function getOkFileSuffix()
    {
        return $this->getFileResolverConfiguration()->getOkFileSuffix();
    }

    /**
     * Initializes the file resolver for the import process with the passed serial.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The source directory to parse for files
     * @param string                                                           $serial  The unique identifier of the actual import process
     *
     * @return void
     * @throws \Exception Is thrown if the configured source directory is not available
     */
    protected function initialize($serial)
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute($serial);

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory "%s" is not available!', $sourceDir));
        }

        // set the source directory
        $this->setSourceDir($sourceDir);
    }

    /**
     * Sets the subject configuration instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return void
     */
    public function setSubjectConfiguration(SubjectConfigurationInterface $subjectConfiguration)
    {
        $this->subjectConfiguration = $subjectConfiguration;
    }

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getSubjectConfiguration()
    {
        return $this->subjectConfiguration;
    }

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     * @throws \Exception Is thrown, when the source directory is NOT available
     */
    public function loadFiles($serial)
    {

        // clear the filecache
        clearstatcache();

        // initialize the resolver
        $this->initialize($serial);

        // initialize the array with the files matching the suffix found in the source directory
        $files = glob(sprintf('%s/*.%s', $this->getSourceDir(), $this->getSuffix()));

        // sort the files for the apropriate order
        usort($files, function ($a, $b) {
            return strcmp($a, $b);
        });

        // return the sorted files
        return $files;
    }
}
