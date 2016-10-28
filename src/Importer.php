<?php

/**
 * TechDivision\Import\Importer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import;

use Rhumsaa\Uuid\Uuid;
use Psr\Log\LoggerInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Importer
{

    /**
     * The actions unique serial.
     *
     * @var string
     */
    protected $serial;

    /**
     * The system logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

    /**
     * The RegistryProcessor instance to handle running threads.
     *
     * @var \TechDivision\Importer\Services\RegistryProcessor
     */
    protected $registryProcessor;

    /**
     * The processor to read/write the necessary product data.
     *
     * @var \TechDivision\Importer\Services\ProductProcessor
     */
    protected $productProcessor;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Set's the unique serial for this import process.
     *
     * @param string $serial The unique serial
     *
     * @return void
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
    }

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set's the system logger.
     *
     * @param \Psr\Log\LoggerInterface $systemLogger The system logger
     *
     * @return void
     */
    public function setSystemLogger(LoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
    }

    /**
     * Return's the system logger.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {
        return $this->systemLogger;
    }

    /**
     * Sets's the RegistryProcessor instance to handle the running threads.
     *
     * @param \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface $registryProcessor
     *
     * @return void
     */
    public function setRegistryProcessor($registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The instance
     */
    public function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Set's the product processor instance.
     *
     * @param Importer\Csv\Services\Pdo\ProductProcessor $productProcessor The product processor instance
     *
     * @return void
     */
    public function setProductProcessor($productProcessor)
    {
        $this->productProcessor = $productProcessor;
    }

    /**
     * Return's the product processor instance.
     *
     * @return \Importer\Csv\Services\Pdo\ProductProcessor The product processor instance
     */
    public function getProductProcessor()
    {
        return $this->productProcessor;
    }

    /**
     * Set's the system configuration.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The system configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\ConfigurationInterface The system configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->getConfiguration()->getPrefix();
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->getConfiguration()->getSourceDir();
    }

    /**
     * Parse the temporary upload directory for new files to be imported.
     *
     * @return void
     */
    public function import()
    {

        // track the start time
        $startTime = microtime(true);

        // generate the serial for the new job
        $this->setSerial(Uuid::uuid4()->__toString());

        // prepare the global data for the import process
        $this->start();
        $this->setUp();
        $this->parseDirectory();
        $this->processSubjects();
        $this->tearDown();
        $this->finish();

        // track the time needed for the import in seconds
        $endTime = microtime(true) - $startTime;

        // log a message that import has been finished
        $this->getSystemLogger()->debug(sprintf('Successfully finished import with serial %s in %f s', $this->getSerial(), $endTime));
    }

    /**
     * This method start's the import process by initializing
     * the status and appends it to the registry.
     *
     * @return void
     */
    public function start()
    {

        // initialize the status
        $status = array(
            'status'     => 1,
            'files'      => array(),
            'variations' => array()
        );

        // append it to the registry
        $this->getRegistryProcessor()->setAttribute($this->getSerial(), $status);
    }

    /**
     * Prepares the global data for the import process.
     *
     * @return void
     */
    public function setUp()
    {

        try {
            // load the registry
            $productProcessor = $this->getProductProcessor();
            $registryProcessor = $this->getRegistryProcessor();

            // initialize the array for the global data
            $globalData = array();

            // initialize the global data
            $globalData[RegistryKeys::STORES] = $productProcessor->getStores();
            $globalData[RegistryKeys::TAX_CLASSES] = $productProcessor->getTaxClasses();
            $globalData[RegistryKeys::STORE_WEBSITES] = $productProcessor->getStoreWebsites();
            $globalData[RegistryKeys::ATTRIBUTE_SETS] = $eavAttributeSets = $productProcessor->getEavAttributeSetsByEntityTypeId(4);

            // prepare an array with the EAV attributes grouped by their attribute set name as keys
            $eavAttributes = array();
            foreach (array_keys($eavAttributeSets) as $eavAttributeSetName) {
                $eavAttributes[$eavAttributeSetName] = $productProcessor->getEavAttributesByEntityTypeIdAndAttributeSetName(4, $eavAttributeSetName);
            }

            // initialize the array with theEAV attributes
            $globalData[RegistryKeys::EAV_ATTRIBUTES] = $eavAttributes;

            // add the status with the global data
            $registryProcessor->mergeAttributesRecursive($this->getSerial(), array('globalData' => $globalData));

        } catch (\Exception $e) {
            $this->getSystemLogger()->error($e->__toString());
        }
    }

    /**
     * This method parse's the application server's temporary upload dirctory for
     * new CSV files with product bunches that have to be imported.
     *
     * If a file, that has to be imported, will be found, a flag file with the
     * suffix .inProgress will be created. That prevents files to imported multiple
     * times.
     *
     * @return void
     */
    public function parseDirectory()
    {

        // load system logger and entity manager
        $systemLogger = $this->getSystemLogger();
        $registryProcessor = $this->getRegistryProcessor();

        // init file iterator on deployment directory
        $fileIterator = new \FilesystemIterator($sourceDir = $this->getSourceDir());

        // clear the filecache
        clearstatcache();

        // prepare the regex to find the files to be imported
        $regex = sprintf('/^.*\/%s.*\\.csv$/', $this->getPrefix());

        // log a debug message
        $systemLogger->debug(
            sprintf('Now checking directory %s for files with regex %s to import', $sourceDir, $regex)
        );

        // initialize the array with the files that have to be imported
        $filesToProcess = array();

        // iterate through all CSV files and start import process
        foreach (new \RegexIterator($fileIterator, $regex) as $filename) {
            try {
                // prepare the flag filenames
                $inProgressFilename = sprintf('%s.inProgress', $filename);
                $importedFilename = sprintf('%s.imported', $filename);
                $failedFilename = sprintf('%s.failed', $filename);

                // query whether or not the file has already been imported
                if (is_file($failedFilename) ||
                    is_file($importedFilename) ||
                    is_file($inProgressFilename)
                ) {
                    // log a debug message
                    $systemLogger->debug(
                        sprintf('Import running, found inProgress file %s', $inProgressFilename)
                    );
                    // ignore the file
                    continue;
                }

                // log the filename we'll process now
                $systemLogger->debug(sprintf('Now start importing file %s!', $filename));

                // flag file as in progress
                touch($inProgressFilename);

                // add the file to the list with filenames
                $filesToProcess[Uuid::uuid4()->__toString()] = array('filename' => $filename->getPathname(), 'status' => 0);

                // rename flag file, because import has been successfull
                rename($inProgressFilename, $importedFilename);

            } catch (\Exception $e) {
                // rename the flag file, because import failed
                rename($inProgressFilename, $failedFilename);

                // log a message that the file import failed
                $this->getSystemLogger()->error($e->__toString());
            }
        }

        // query whether or not we've CSV files that has to be processed
        if (sizeof($filesToProcess) > 0) {
            $registryProcessor->mergeAttributesRecursive($this->getSerial(), array('files' => $filesToProcess));
        }
    }

    /**
     * Process all the subjects defined in the system configuration.
     *
     * @return void
     */
    public function processSubjects()
    {

        // load system logger and registry
        $systemLogger = $this->getSystemLogger();

        // load the subjects
        $subjects = $this->getConfiguration()->getSubjects();

        // process all the subjects found in the system configuration
        foreach ($subjects as $subject) {
            // process the subject and and log a message that the subject has been processed
            $this->processSubject($subject);
            $systemLogger->info(sprintf('Successfully processed subject %s!', $subject->getClassName()));
        }
    }

    /**
     * Process the subject with the passed name/identifier.
     *
     * @param \TechDivision\Import\Configuration\Subject $subject The subject configuration
     *
     * @return void
     */
    public function processSubject($subject)
    {

        // load the registry instance
        $registryProcessor = $this->getRegistryProcessor();

        // load the status information of the actual import
        $status = $registryProcessor->getAttribute($serial = $this->getSerial());

        // if no files have been found, stop processing
        if (sizeof($uids = array_keys($status[$subject->getIdentifier()])) === 0) {
            return;
        }

        // start processing the subject on all found UIDs
        foreach ($uids as $uid) {
            $this->subjectFactory($subject)->import($serial, $uid);
        }
    }

    /**
     * Factory method to create new handler instances.
     *
     * @param \TechDivision\Import\Configuration\Subject $subject The subject configuration
     *
     * @return object The handler instance
     */
    protected function subjectFactory($subject)
    {

        // load the subject class name
        $className = $subject->getClassName();

        // initialize a new handler with the passed class name
        $instance = new $className();

        // $instance the handler instance
        $instance->setConfiguration($subject);
        $instance->setSystemLogger($this->getSystemLogger());
        $instance->setProductProcessor($this->getProductProcessor());
        $instance->setRegistryProcessor($this->getRegistryProcessor());

        // return the subject instance
        return $instance;
    }

    /**
     * Lifecycle callback that will be inovked after the
     * import process has been finished.
     *
     * @return void
     */
    public function tearDown()
    {
        // clean up here
    }

    /**
     * This method finishes the import process and cleans the registry.
     *
     * @return void
     */
    public function finish()
    {
        $this->getRegistryProcessor()->removeAttribute($this->getSerial());
    }
}
