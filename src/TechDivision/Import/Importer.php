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
     * The prefix for the import files.
     *
     * @var string
     */
    protected $prefix;

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
     * The source directory that has to be watched for new files.
     *
     * @var string
     */
    protected $sourceDir;

    /**
     * The default source date format.
     *
     * @var string
     */
    protected $sourceDateFormat = 'n/d/y, g:i A';

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
     * Set's the prefix for the import files.
     *
     * @param string $prefix The prefix
     *
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->prefix;
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
     * Set's the source directory that has to be watched for new files.
     *
     * @param string $sourceDir The source directory
     *
     * @return void
     */
    public function setSourceDir($sourceDir)
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * Set's the source date format to use.
     *
     * @param string $sourceFormat The source date format
     *
     * @return void
     */
    public function setSourceDateFormat($sourceDateFormat)
    {
        $this->sourceDateFormat = $sourceDateFormat;
    }

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->sourceDateFormat;
    }

    /**
     * Parse the temporary upload directory for new files to be imported.
     *
     * @param string $prefix The import filename prefix
     *
     * @return void
     */
    public function import($prefix)
    {

        // track the start time
        $startTime = microtime(true);

        // generate the serial for the new job
        $this->setSerial(Uuid::uuid4()->__toString());

        // the file prefix for the CSV files with the product bunches
        $this->setPrefix($prefix);

        // prepare the global data for the import process
        $this->start();
        $this->setUp();
        $this->parseDirectory();
        $this->processBunches();
        $this->processVariations();
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
        $regex = sprintf('/^.*\/%s.*\\.csv$/', $this->prefix);

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
     * This method start's the import process for the products itself. A bunch in this case means
     * a CSV file with a specific number of products that have to be imported. The order of the
     * products within the bunches is not relevant, as relations or variants will be processed after
     * all products have been imported.
     *
     * Starting the import process means to send a message to the message-queue with the import
     * ID the process the products for. The real import process finally takes place in the class
     * <code>ProductImportVariantAction</code>.
     *
     * @return void
     * @see Import\Csv\Actions\ProductImportBunchAction::import()
     */
    public function processBunches()
    {

        // load system logger and registry
        $systemLogger = $this->getSystemLogger();
        $registryProcessor = $this->getRegistryProcessor();

        // load the status information of the actual import
        $status = $registryProcessor->getAttribute($serial = $this->getSerial());

        // if no files have been found, stop processing
        if (sizeof($uids = array_keys($status['files'])) === 0) {
            return;
        }

        // start processing the found CSV files
        foreach ($uids as $uid) {
            $this->processHandler('TechDivision\Import\Handler\BunchHandler', $serial, $uid);
        }

        // log a message that all bunches have been processed
        $systemLogger->info(sprintf('All bunches for job %s has been processed successfully!', $serial));
    }

    /**
     * This method start's the import process for all product variations (configurable products).
     *
     * Starting the import process means to send a message to the message-queue with the import
     * ID the process the variations for. The real import process finally takes place in the class
     * <code>ProductImportVariantAction</code>.
     *
     * @return void
     * @see Import\Csv\Actions\ProductImportVariantAction::import()
     */
    public function processVariations()
    {

        // load system logger and registry
        $systemLogger = $this->getSystemLogger();
        $registryProcessor = $this->getRegistryProcessor();

        // load the status information of the actual import
        $status = $registryProcessor->getAttribute($serial = $this->getSerial());

        // if no variations have been found, stop processing
        if (sizeof($status['variations']) === 0) {
            return;
        }

        // start processing the found CSV files
        foreach (array_keys($status['variations']) as $uid) {
            $this->processHandler('TechDivision\Import\Handler\VariantHandler', $serial, $uid);
        }

        // log a message that all variations have been processed
        $systemLogger->info(sprintf('All variations for job %s has been processed successfully!', $serial));
    }

    /**
     * Create's a new handler instance and processes it.
     *
     * @param string $className The handler class name to create the instance
     * @param string $serial    The serial for the actual import process
     * @param string $uid       The UID of the filename to process
     *
     * @return void
     */
    protected function processHandler($className, $serial, $uid)
    {
        $this->handlerFactory($className)->import($serial, $uid);
    }

    /**
     * Factory method to create new handler instances.
     *
     * @param string $className The handler class name to create the instance
     *
     * @return object The handler instance
     */
    protected function handlerFactory($className)
    {

        // initialize a new handler with the passed class name
        $handler = new $className();

        // initialize the handler instance
        $handler->setSystemLogger($this->getSystemLogger());
        $handler->setSourceDateFormat($this->getSourceDateFormat());
        $handler->setProductProcessor($this->getProductProcessor());
        $handler->setRegistryProcessor($this->getRegistryProcessor());

        // return the handler instance
        return $handler;
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
