<?php

/**
 * TechDivision\Import\Cli\Subjects\MoveFilesSubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use League\Event\EmitterInterface;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Utils\Generators\GeneratorInterface;

/**
 * The subject implementation to move the files to their target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MoveFilesSubject extends AbstractSubject
{

    /**
     *The loader for the proposed filename.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $proposedFilenameLoader;

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \Doctrine\Common\Collections\Collection                  $systemLoggers              The array with the system loggers instances
     * @param \League\Event\EmitterInterface                           $emitter                    The event emitter instance
     * @param \TechDivision\Import\Loaders\LoaderInterface             $proposedFilenameLoader     The loader for the proposed filename
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        Collection $systemLoggers,
        EmitterInterface $emitter,
        LoaderInterface $proposedFilenameLoader
    ) {

        // pass the instances to the parent constructor
        parent::__construct($registryProcessor, $coreConfigDataUidGenerator, $systemLoggers, $emitter);

        // initialize the proposed filename loader
        $this->proposedFilenameLoader = $proposedFilenameLoader;
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update target and source directory for the next subject
        $registryProcessor->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            array(
                RegistryKeys::TARGET_DIRECTORY => $newSourceDir = $this->getNewSourceDir($serial),
                RegistryKeys::SOURCE_DIRECTORY => $newSourceDir
            )
        );

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated source directory to %s', get_class($this), $newSourceDir)
        );

        // invoke the parent method
        parent::tearDown($serial);
    }

    /**
     * Return's the header mappings for the actual entity.
     *
     * @return array The header mappings
     */
    public function getHeaderMappings()
    {
        return array();
    }

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings()
    {
        return array();
    }

    /**
     * Imports the content of the file with the passed filename.
     *
     * @param string $serial   The serial of the actual import
     * @param string $filename The filename to process
     *
     * @return void
     * @throws \Exception Is thrown, if the import can't be processed
     */
    public function import($serial, $filename)
    {

        // initialize the serial/filename
        $this->setSerial($serial);
        $this->setFilename($filename);

        // prepare the new filename, e. g. in case a specific filename has been
        // passed on the command line as second argument
        $newFilename = $this->getNewFilename($filename);

        // prepare the new source directory
        $newSourceDir = $this->prepareNewSourceDir($serial);

        // move the file to the new source directory
        $this->rename($filename, $target = sprintf('%s/%s', $newSourceDir, $newFilename));

        // log a message that the original file has been moved
        // to the target directory, and probably renamed
        $this->getSystemLogger()->notice(sprintf('Moved file %s to %s', $filename, $target));

        // update the status
        $this->mergeStatus(
            array(
                RegistryKeys::STATUS => array(
                    RegistryKeys::FILES => array(
                        $filename => array(
                            $this->getUniqueId() => array(
                                RegistryKeys::STATUS         => 1,
                                RegistryKeys::SKIPPED_ROWS   => $this->getSkippedRows(),
                                RegistryKeys::PROCESSED_ROWS => $this->getLineNumber() - 1
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @param string $serial The serial of the actual import
     *
     * @return string The new source directory
     */
    protected function getNewSourceDir(string $serial) : string
    {
        return sprintf('%s/%s', $this->getConfiguration()->getTargetDir(), $serial);
    }

    /**
     * Prepares the new source directory based on the passed serial.
     *
     * @param string $serial The serial to prepare the new source directory with
     *
     * @return string The new source directory
     */
    protected function prepareNewSourceDir(string $serial) : string
    {

        // initialize the path for the new source directory
        $newSourceDir = $this->getNewSourceDir($serial);

        // query whether the new source directory has to be created or not
        if ($this->isDir($newSourceDir) === false) {
            $this->mkdir($newSourceDir);
        }

        // return the new source directory
        return $newSourceDir;
    }

    /**
     * Return's the loader for the proposed filename.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The proposed filename loader
     */
    protected function getProposedFilenameLoader() : LoaderInterface
    {
        return $this->proposedFilenameLoader;
    }

    /**
     * Prepares and returns the new filename in case a file has been specified
     * as second commanline line argument.
     *
     * @param string $filename filename
     *
     * @return string
     */
    protected function getNewFilename(string $filename) : string
    {

        // extract the new filename from the path
        $newFilename = basename($filename);

        // load the subject and the global configuration
        $subjectConfiguration = $this->getConfiguration();
        $configuration = $subjectConfiguration->getConfiguration();

        // query whether or not specifc filename has been passed as command
        // line argument, if yes make sure it is the passed one
        if ($configuration instanceof FileResolverConfigurationInterface && $configuration->getFilename() === realpath($filename)) {
            // load the first prefixed subject, because we've to prepare
            // a filename that matches this fileresolver configuration
            /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $firstPrefixedSubject */
            $firstPrefixedSubject = $configuration->getFirstPrefixedSubject();
            // prepare the proposed filename based on the filresolver configuration
            $this->getProposedFilenameLoader()->setFileResolverConfiguration($firstPrefixedSubject->getFileResolver());
            $newFilename = $this->getProposedFilenameLoader()->load($newFilename);
        }

        // return the original filename
        return $newFilename;
    }
}
