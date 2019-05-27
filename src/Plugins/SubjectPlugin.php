<?php

/**
 * TechDivision\Import\Plugins\SubjectPlugin
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface;

/**
 * Plugin that processes the subjects.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectPlugin extends AbstractPlugin implements SubjectAwarePluginInterface
{

    /**
     * The matches for the last processed CSV filename.
     *
     * @var array
     */
    protected $matches = array();

    /**
     * The number of imported bunches.
     *
     * @var integer
     */
    protected $bunches = 0;

    /**
     * The subject executor instance.
     *
     * @var \TechDivision\Import\Plugins\SubjectExecutorInterface
     */
    protected $subjectExecutor;

    /**
     * The file resolver factory instance.
     *
     * @var \TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface
     */
    protected $fileResolverFactory;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\ApplicationInterface                               $application         The application instance
     * @param \TechDivision\Import\Plugins\SubjectExecutorInterface                   $subjectExecutor     The subject executor instance
     * @param \TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface $fileResolverFactory The file resolver instance
     */
    public function __construct(
        ApplicationInterface $application,
        SubjectExecutorInterface $subjectExecutor,
        FileResolverFactoryInterface $fileResolverFactory
    ) {

        // call the parent constructor
        parent::__construct($application);

        // set the subject executor and the file resolver factory
        $this->subjectExecutor = $subjectExecutor;
        $this->fileResolverFactory = $fileResolverFactory;
    }


    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        try {
            // immediately add the PID to lock this import process
            $this->lock();

            // load the plugin's subjects
            $subjects = $this->getPluginConfiguration()->getSubjects();

            // initialize the array for the status
            $status = array();

            // initialize the status information for the subjects
            /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
            foreach ($subjects as $subject) {
                $status[$subject->getPrefix()] = array();
            }

            // and update it in the registry
            $this->getRegistryProcessor()->mergeAttributesRecursive($this->getSerial(), $status);

            // process all the subjects found in the system configuration
            /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
            foreach ($subjects as $subject) {
                $this->processSubject($subject);
            }

            // update the number of imported bunches
            $this->getRegistryProcessor()->mergeAttributesRecursive(
                $this->getSerial(),
                array(RegistryKeys::BUNCHES => $this->bunches)
            );

            // stop the application if we don't process ANY bunch
            if ($this->bunches === 0) {
                $this->getApplication()->stop(
                    sprintf(
                        'Operation %s has been stopped by %s, because no import files can be found in directory %s',
                        $this->getConfiguration()->getOperationName(),
                        get_class($this),
                        $this->getConfiguration()->getSourceDir()
                    )
                );
            }

            // finally, if a PID has been set (because CSV files has been found),
            // remove it from the PID file to unlock the importer
            $this->unlock();
        } catch (\Exception $e) {
            // finally, if a PID has been set (because CSV files has been found),
            // remove it from the PID file to unlock the importer
            $this->unlock();

            // re-throw the exception
            throw $e;
        }
    }

    /**
     * Process the subject with the passed name/identifier.
     *
     * We create a new, fresh and separate subject for EVERY file here, because this would be
     * the starting point to parallelize the import process in a multithreaded/multiprocessed
     * environment.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject configuration
     *
     * @return void
     */
    protected function processSubject(SubjectConfigurationInterface $subject)
    {

        // initialize the bunch number
        $bunches = 0;

        // load the file resolver for the subject with the passed configuration
        $fileResolver = $this->fileResolverFactory->createFileResolver($subject);

        // load the files
        $files = $fileResolver->loadFiles($serial = $this->getSerial());

        // iterate through all CSV files and process the subjects
        foreach ($files as $filename) {
            // query whether or not that the file is part of the actual bunch
            if ($fileResolver->shouldBeHandled($filename)) {
                // clean-up the OK file, if needed
                $fileResolver->cleanUpOkFile($filename);

                // initialize the subject and import the bunch
                $this->subjectExecutor->execute($subject, $fileResolver->getMatches(), $serial, $filename);

                // raise the number of the imported bunches
                $bunches++;
            }
        }

        // raise the bunch number by the imported bunches
        $this->bunches = $this->bunches + $bunches;

        // reset the file resolver for making it ready parsing the files of the next subject
        $fileResolver->reset();

        // and and log a message that the subject has been processed
        $this->getSystemLogger()->debug(
            sprintf('Successfully processed subject %s with %d bunch(es)!', $subject->getId(), $bunches)
        );
    }
}
