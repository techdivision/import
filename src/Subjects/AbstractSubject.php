<?php

/**
 * TechDivision\Import\Subjects\AbstractSubject
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

namespace TechDivision\Import\Subjects;

use Doctrine\Common\Collections\Collection;
use TechDivision\Import\RowTrait;
use TechDivision\Import\HeaderTrait;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Callbacks\CallbackInterface;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Adapter\ImportAdapterInterface;
use TechDivision\Import\Exceptions\WrappedColumnException;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * An abstract subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractSubject implements SubjectInterface
{

    /**
     * The trait that provides basic filesystem handling functionality.
     *
     * @var TechDivision\Import\Subjects\FilesystemTrait
     */
    use FilesystemTrait;

    /**
     * The trait that provides basic filesystem handling functionality.
     *
     * @var TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The trait that provides header handling functionality.
     *
     * @var TechDivision\Import\HeaderTrait
     */
    use HeaderTrait;

    /**
     * The trait that provides row handling functionality.
     *
     * @var TechDivision\Import\RowTrait
     */
    use RowTrait;

    /**
     * The name of the file to be imported.
     *
     * @var string
     */
    protected $filename;

    /**
     * The actual line number.
     *
     * @var integer
     */
    protected $lineNumber = 0;

    /**
     * The actual operation name.
     *
     * @var string
     */
    protected $operationName ;

    /**
     * The flag that stop's overserver execution on the actual row.
     *
     * @var boolean
     */
    protected $skipRow = false;

    /**
     * The import adapter instance.
     *
     * @var \TechDivision\Import\Adapter\ImportAdapterInterface
     */
    protected $importAdapter;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    protected $configuration;

    /**
     * The RegistryProcessor instance to handle running threads.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The actions unique serial.
     *
     * @var string
     */
    protected $serial;

    /**
     * Array with the subject's observers.
     *
     * @var array
     */
    protected $observers = array();

    /**
     * Array with the subject's callbacks.
     *
     * @var array
     */
    protected $callbacks = array();

    /**
     * The subject's callback mappings.
     *
     * @var array
     */
    protected $callbackMappings = array();

    /**
     * The available root categories.
     *
     * @var array
     */
    protected $rootCategories = array();

    /**
     * The Magento configuration.
     *
     * @var array
     */
    protected $coreConfigData = array();

    /**
     * The available stores.
     *
     * @var array
     */
    protected $stores = array();

    /**
     * The available websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * The default store.
     *
     * @var array
     */
    protected $defaultStore;

    /**
     * The store view code the create the product/attributes for.
     *
     * @var string
     */
    protected $storeViewCode;

    /**
     * The UID generator for the core config data.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    protected $coreConfigDataUidGenerator;

    /**
     * UNIX timestamp with the date the last row counter has been logged.
     *
     * @var integer
     */
    protected $lastLog = 0;

    /**
     * The number of the last line that has been logged with the row counter
     * @var integer
     */
    protected $lastLineNumber = 0;

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \Doctrine\Common\Collections\Collection                  $systemLoggers              The array with the system loggers instances
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        Collection $systemLoggers
    ) {
        $this->systemLoggers = $systemLoggers;
        $this->registryProcessor = $registryProcessor;
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
    }

    /**
     * Set's the name of the file to import
     *
     * @param string $filename The filename
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Return's the name of the file to import.
     *
     * @return string The filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set's the actual operation name.
     *
     * @param string $operationName The actual operation name
     *
     * @return void
     */
    public function setOperationName($operationName)
    {
        $this->operationName = $operationName;
    }

    /**
     * Return's the actual operation name.
     *
     * @return string
     */
    public function getOperationName()
    {
        return $this->operationName;
    }

    /**
     * Set's the actual line number.
     *
     * @param integer $lineNumber The line number
     *
     * @return void
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
    }

    /**
     * Return's the actual line number.
     *
     * @return integer The line number
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * Stop's observer execution on the actual row.
     *
     * @return void
     */
    public function skipRow()
    {
        $this->skipRow = true;
    }

    /**
     * Return's the default callback mappings.
     *
     * @return array The default callback mappings
     */
    public function getDefaultCallbackMappings()
    {
        return array();
    }

    /**
     * Tries to format the passed value to a valid date with format 'Y-m-d H:i:s'.
     * If the passed value is NOT a valid date, NULL will be returned.
     *
     * @param string $value The value to format
     *
     * @return string|null The formatted date or NULL if the date is not valid
     */
    public function formatDate($value)
    {

        // create a DateTime instance from the passed value
        if ($dateTime = \DateTime::createFromFormat($this->getSourceDateFormat(), $value)) {
            return $dateTime->format('Y-m-d H:i:s');
        }

        // return NULL, if the passed value is NOT a valid date
        return null;
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string      $value     The value to extract
     * @param string|null $delimiter The delimiter used to extrace the elements
     *
     * @return array The exploded values
     */
    public function explode($value, $delimiter = null)
    {
        // load the global configuration
        $configuration = $this->getConfiguration();

        // initializet delimiter, enclosure and escape char
        $delimiter = $delimiter ? $delimiter : $configuration->getDelimiter();
        $enclosure = $configuration->getEnclosure();
        $escape = $configuration->getEscape();

        // parse and return the found data as array
        return str_getcsv($value, $delimiter, $enclosure, $escape);
    }

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    public function isDebugMode()
    {
        return $this->getConfiguration()->isDebugMode();
    }

    /**
     * Set's the subject configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $configuration The subject configuration
     *
     * @return void
     */
    public function setConfiguration(SubjectConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the subject configuration.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set's the import adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ImportAdapterInterface $importAdapter The import adapter instance
     *
     * @return void
     */
    public function setImportAdapter(ImportAdapterInterface $importAdapter)
    {
        $this->importAdapter = $importAdapter;
    }

    /**
     * Return's the import adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ImportAdapterInterface The import adapter instance
     */
    public function getImportAdapter()
    {
        return $this->importAdapter;
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    public function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

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
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->getConfiguration()->getSourceDateFormat();
    }

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    public function getMultipleFieldDelimiter()
    {
        return $this->getConfiguration()->getMultipleFieldDelimiter();
    }

    /**
     * Return's the multiple value delimiter character to use, default value is comma (|).
     *
     * @return string The multiple value delimiter character
     */
    public function getMultipleValueDelimiter()
    {
        return $this->getConfiguration()->getMultipleValueDelimiter();
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($serial);

        // load the global data we've prepared initially
        $this->stores = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORES];
        $this->defaultStore = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::DEFAULT_STORE];
        $this->storeWebsites  = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];
        $this->rootCategories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ROOT_CATEGORIES];
        $this->coreConfigData = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CORE_CONFIG_DATA];

        // initialize the operation name
        $this->operationName = $this->getConfiguration()->getConfiguration()->getOperationName();

        // merge the callback mappings with the mappings from the child instance
        $this->callbackMappings = array_merge($this->callbackMappings, $this->getDefaultCallbackMappings());

        // merge the header mappings with the values found in the configuration
        $this->headerMappings = array_merge($this->headerMappings, $this->getConfiguration()->getHeaderMappings());

        // merge the callback mappings the the one from the configuration file
        foreach ($this->getConfiguration()->getCallbacks() as $callbackMappings) {
            foreach ($callbackMappings as $attributeCode => $mappings) {
                // write a log message, that default callback configuration will
                // be overwritten with the one from the configuration file
                if (isset($this->callbackMappings[$attributeCode])) {
                    $this->getSystemLogger()->notice(
                        sprintf('Now override callback mappings for attribute %s with values found in configuration file', $attributeCode)
                    );
                }

                // override the attributes callbacks
                $this->callbackMappings[$attributeCode] = $mappings;
            }
        }
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

        // update the source directory for the next subject
        $registryProcessor->mergeAttributesRecursive(
            $serial,
            array(
                RegistryKeys::SOURCE_DIRECTORY => $newSourceDir = $this->getNewSourceDir($serial),
                RegistryKeys::FILES => array($this->getFilename() => array(RegistryKeys::STATUS => 1))
            )
        );

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated source directory to %s', get_class($this), $newSourceDir)
        );
    }

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    public function getTargetDir()
    {
        return $this->getNewSourceDir($this->getSerial());
    }

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @param string $serial The serial of the actual import
     *
     * @return string The new source directory
     */
    public function getNewSourceDir($serial)
    {
        return sprintf('%s/%s', $this->getConfiguration()->getTargetDir(), $serial);
    }

    /**
     * Register the passed observer with the specific type.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer to register
     * @param string                                           $type     The type to register the observer with
     *
     * @return void
     */
    public function registerObserver(ObserverInterface $observer, $type)
    {

        // query whether or not the array with the callbacks for the
        // passed type has already been initialized, or not
        if (!isset($this->observers[$type])) {
            $this->observers[$type] = array();
        }

        // append the callback with the instance of the passed type
        $this->observers[$type][] = $observer;
    }

    /**
     * Register the passed callback with the specific type.
     *
     * @param \TechDivision\Import\Callbacks\CallbackInterface $callback The subject to register the callbacks for
     * @param string                                           $type     The type to register the callback with
     *
     * @return void
     */
    public function registerCallback(CallbackInterface $callback, $type)
    {

        // query whether or not the array with the callbacks for the
        // passed type has already been initialized, or not
        if (!isset($this->callbacks[$type])) {
            $this->callbacks[$type] = array();
        }

        // append the callback with the instance of the passed type
        $this->callbacks[$type][] = $callback;
    }

    /**
     * Return's the array with callbacks for the passed type.
     *
     * @param string $type The type of the callbacks to return
     *
     * @return array The callbacks
     */
    public function getCallbacksByType($type)
    {

        // initialize the array for the callbacks
        $callbacks = array();

        // query whether or not callbacks for the type are available
        if (isset($this->callbacks[$type])) {
            $callbacks = $this->callbacks[$type];
        }

        // return the array with the type's callbacks
        return $callbacks;
    }

    /**
     * Return's the array with the available observers.
     *
     * @return array The observers
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Return's the array with the available callbacks.
     *
     * @return array The callbacks
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * Return's the callback mappings for this subject.
     *
     * @return array The array with the subject's callback mappings
     */
    public function getCallbackMappings()
    {
        return $this->callbackMappings;
    }

    /**
     * Imports the content of the file with the passed filename.
     *
     *
     * @param string $serial   The serial of the actual import
     * @param string $filename The filename to process
     *
     * @return void
     * @throws \Exception Is thrown, if the import can't be processed
     */
    public function import($serial, $filename)
    {

        try {
            // stop processing, if the filename doesn't match
            if (!$this->match($filename)) {
                return;
            }

            // load the system logger instance
            $systemLogger = $this->getSystemLogger();

            // prepare the flag filenames
            $inProgressFilename = sprintf('%s.inProgress', $filename);
            $importedFilename = sprintf('%s.imported', $filename);
            $failedFilename = sprintf('%s.failed', $filename);

            // query whether or not the file has already been imported
            if ($this->isFile($failedFilename) ||
                $this->isFile($importedFilename) ||
                $this->isFile($inProgressFilename)
            ) {
                // log a debug message and exit
                $systemLogger->debug(sprintf('Import running, found inProgress file %s', $inProgressFilename));
                return;
            }

            // flag file as in progress
            $this->touch($inProgressFilename);

            // track the start time
            $startTime = microtime(true);

            // initialize the serial/filename
            $this->setSerial($serial);
            $this->setFilename($filename);

            // initialize the last time we've logged the counter with the processed rows per minute
            $this->lastLog = time();

            // log a message that the file has to be imported
            $systemLogger->info(sprintf('Now start processing file %s', $filename));

            // let the adapter process the file
            $this->getImportAdapter()->import(array($this, 'importRow'), $filename);

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file has successfully been imported
            $systemLogger->info(sprintf('Successfully processed file %s with %d lines in %f s', $filename, $this->lineNumber, $endTime));

            // rename flag file, because import has been successfull
            $this->rename($inProgressFilename, $importedFilename);

        } catch (\Exception $e) {
            // rename the flag file, because import failed and write the stack trace
            $this->rename($inProgressFilename, $failedFilename);
            $this->write($failedFilename, $e->__toString());

            // do not wrap the exception if not already done
            if ($e instanceof WrappedColumnException) {
                throw $e;
            }

            // else wrap and throw the exception
            throw $this->wrapException(array(), $e);
        }
    }

    /**
     * This method queries whether or not the passed filename matches
     * the pattern, based on the subjects configured prefix.
     *
     * @param string $filename The filename to match
     *
     * @return boolean TRUE if the filename matches, else FALSE
     */
    protected function match($filename)
    {

        // prepare the pattern to query whether the file has to be processed or not
        $pattern = sprintf(
            '/^.*\/%s.*\\.%s$/',
            $this->getConfiguration()->getPrefix(),
            $this->getConfiguration()->getSuffix()
        );

        // stop processing, if the filename doesn't match
        return (boolean) preg_match($pattern, $filename);
    }

    /**
     * Imports the passed row into the database. If the import failed, the exception
     * will be catched and logged, but the import process will be continued.
     *
     * @param array $row The row with the data to be imported
     *
     * @return void
     */
    public function importRow(array $row)
    {

        // initialize the row
        $this->row = $row;

        // raise the line number and reset the skip row flag
        $this->lineNumber++;
        $this->skipRow = false;

        // initialize the headers with the columns from the first line
        if (sizeof($this->headers) === 0) {
            foreach ($this->row as $value => $key) {
                $this->headers[$this->mapAttributeCodeByHeaderMapping($key)] = $value;
            }
            return;
        }

        // process the observers
        foreach ($this->getObservers() as $observers) {
            // invoke the pre-import/import and post-import observers
            foreach ($observers as $observer) {
                // query whether or not we have to skip the row
                if ($this->skipRow) {
                    // log a debug message with the actual line nr/file information
                    $this->getSystemLogger()->warning(
                        $this->appendExceptionSuffix(
                            sprintf(
                                'Skip processing operation "%s" after observer "%s"',
                                $this->operationName,
                                $this->getConfiguration()->getId()
                            )
                        )
                    );

                    // skip the row
                    break 2;
                }

                // if not, set the subject and process the observer
                if ($observer instanceof ObserverInterface) {
                    $this->row = $observer->handle($this);
                }
            }
        }

        // query whether or not a minute has been passed
        if ($this->lastLog < time() - 60) {
            // log the number processed rows per minute
            $this->getSystemLogger()->info(
                sprintf(
                    'Successfully processed "%d (%d)" rows per minute of file "%s"',
                    $this->lineNumber - $this->lastLineNumber,
                    $this->lineNumber,
                    $this->getFilename()
                )
            );

            // reset the last log time and the line number
            $this->lastLog = time();
            $this->lastLineNumber = $this->lineNumber;
        }

        // log a debug message with the actual line nr/file information
        $this->getSystemLogger()->debug(
            $this->appendExceptionSuffix(
                sprintf(
                    'Successfully processed operation "%s"',
                    $this->operationName
                )
            )
        );
    }

    /**
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    public function isOkFileNeeded()
    {
        return $this->getConfiguration()->isOkFileNeeded();
    }

    /**
     * Return's the default store.
     *
     * @return array The default store
     */
    public function getDefaultStore()
    {
        return $this->defaultStore;
    }

    /**
     * Return's the default store view code.
     *
     * @return array The default store view code
     */
    public function getDefaultStoreViewCode()
    {
        return $this->defaultStore[MemberNames::CODE];
    }

    /**
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function setStoreViewCode($storeViewCode)
    {
        $this->storeViewCode = $storeViewCode;
    }

    /**
     * Return's the store view code the create the product/attributes for.
     *
     * @param string|null $default The default value to return, if the store view code has not been set
     *
     * @return string The store view code
     */
    public function getStoreViewCode($default = null)
    {

        // return the store view code, if available
        if ($this->storeViewCode !== null) {
            return $this->storeViewCode;
        }

        // if NOT and a default code is available
        if ($default !== null) {
            // return the default value
            return $default;
        }

        // return the default store view code
        return $this->getDefaultStoreViewCode();
    }

    /**
     * Prepare's the store view code in the subject. If the store_view_code row doesn't contain
     * any value, the default code of the default store view will be set.
     *
     * @return void
     */
    public function prepareStoreViewCode()
    {

        // re-set the store view code
        $this->setStoreViewCode(null);

        // initialize the store view code
        if ($storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE)) {
            $this->setStoreViewCode($storeViewCode);
        }
    }

    /**
     * Return's the store ID of the store with the passed store view code
     *
     * @param string $storeViewCode The store view code to return the store ID for
     *
     * @return integer The ID of the store with the passed ID
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getStoreId($storeViewCode)
    {

        // query whether or not, the requested store is available
        if (isset($this->stores[$storeViewCode])) {
            return (integer) $this->stores[$storeViewCode][MemberNames::STORE_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            sprintf(
                'Found invalid store view code %s in file %s on line %d',
                $storeViewCode,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId($default = null)
    {

        // initialize the default store view code, if not passed
        if ($default === null) {
            $default = $this->getDefaultStoreViewCode();
        }

        // load the store view code the create the product/attributes for
        return $this->getStoreId($this->getStoreViewCode($default));
    }

    /**
     * Return's the root category for the actual view store.
     *
     * @return array The store's root category
     * @throws \Exception Is thrown if the root category for the passed store code is NOT available
     */
    public function getRootCategory()
    {

        // load the actual store view code
        $storeViewCode = $this->getStoreViewCode($this->getDefaultStoreViewCode());

        // query weather or not we've a root category or not
        if (isset($this->rootCategories[$storeViewCode])) {
            return $this->rootCategories[$storeViewCode];
        }

        // throw an exception if the root category is NOT available
        throw new \Exception(sprintf('Root category for %s is not available', $storeViewCode));
    }

    /**
     * Return's the Magento configuration value.
     *
     * @param string  $path    The Magento path of the requested configuration value
     * @param mixed   $default The default value that has to be returned, if the requested configuration value is not set
     * @param string  $scope   The scope the configuration value has been set
     * @param integer $scopeId The scope ID the configuration value has been set
     *
     * @return mixed The configuration value
     * @throws \Exception Is thrown, if nor a value can be found or a default value has been passed
     */
    public function getCoreConfigData($path, $default = null, $scope = ScopeKeys::SCOPE_DEFAULT, $scopeId = 0)
    {

        // initialize the core config data
        $coreConfigData = array(
            MemberNames::PATH => $path,
            MemberNames::SCOPE => $scope,
            MemberNames::SCOPE_ID => $scopeId
        );

        // generate the UID from the passed data
        $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

        // iterate over the core config data and try to find the requested configuration value
        if (isset($this->coreConfigData[$uniqueIdentifier])) {
            return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE];
        }

        // query whether or not we've to query for the configuration value on fallback level 'websites' also
        if ($scope === ScopeKeys::SCOPE_STORES) {
            // query whether or not the website with the passed ID is available
            foreach ($this->storeWebsites as $storeWebsite) {
                if ($storeWebsite[MemberNames::WEBSITE_ID] === $scopeId) {
                    // replace scope with 'websites' and website ID
                    $coreConfigData = array_merge(
                        $coreConfigData,
                        array(
                            MemberNames::SCOPE    => ScopeKeys::SCOPE_WEBSITES,
                            MemberNames::SCOPE_ID => $storeWebsite[MemberNames::WEBSITE_ID]
                        )
                    );

                    // generate the UID from the passed data, merged with the 'websites' scope and ID
                    $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

                    // query whether or not, the configuration value on 'websites' level
                    if (isset($this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE])) {
                        return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE];
                    }
                }
            }
        }

        // replace scope with 'default' and scope ID '0'
        $coreConfigData = array_merge(
            $coreConfigData,
            array(
                MemberNames::SCOPE    => ScopeKeys::SCOPE_DEFAULT,
                MemberNames::SCOPE_ID => 0
            )
        );

        // generate the UID from the passed data, merged with the 'default' scope and ID 0
        $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

        // query whether or not, the configuration value on 'default' level
        if (isset($this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE])) {
            return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE];
        }

        // if not, return the passed default value
        if ($default !== null) {
            return $default;
        }

        // throw an exception if no value can be found
        // in the Magento configuration
        throw new \Exception(
            sprintf(
                'Can\'t find a value for configuration "%s-%s-%d" in "core_config_data"',
                $path,
                $scope,
                $scopeId
            )
        );
    }

    /**
     * Resolve the original column name for the passed one.
     *
     * @param string $columnName The column name that has to be resolved
     *
     * @return string|null The original column name
     */
    public function resolveOriginalColumnName($columnName)
    {

        // try to load the original data
        $originalData = $this->getOriginalData();

        // query whether or not original data is available
        if (isset($originalData[ColumnKeys::ORIGINAL_COLUMN_NAMES])) {
            // query whether or not the original column name is available
            if (isset($originalData[ColumnKeys::ORIGINAL_COLUMN_NAMES][$columnName])) {
                return $originalData[ColumnKeys::ORIGINAL_COLUMN_NAMES][$columnName];
            }

            // query whether or a wildcard column name is available
            if (isset($originalData[ColumnKeys::ORIGINAL_COLUMN_NAMES]['*'])) {
                return $originalData[ColumnKeys::ORIGINAL_COLUMN_NAMES]['*'];
            }
        }

        // return the original column name
        return $columnName;
    }

    /**
     * Return's the original data if available, or an empty array.
     *
     * @return array The original data
     */
    public function getOriginalData()
    {

        // initialize the array for the original data
        $originalData = array();

        // query whether or not the column contains original data
        if ($this->hasOriginalData()) {
            // unerialize the original data from the column
            $originalData = unserialize($this->row[$this->headers[ColumnKeys::ORIGINAL_DATA]]);
        }

        // return an empty array, if not
        return $originalData;
    }

    /**
     * Query's whether or not the actual column contains original data like
     * filename, line number and column names.
     *
     * @return boolean TRUE if the actual column contains origin data, else FALSE
     */
    public function hasOriginalData()
    {
        return isset($this->headers[ColumnKeys::ORIGINAL_DATA]) && isset($this->row[$this->headers[ColumnKeys::ORIGINAL_DATA]]);
    }

    /**
     * Wraps the passed exeception into a new one by trying to resolve the original filname,
     * line number and column names and use it for a detailed exception message.
     *
     * @param array      $columnNames The column names that should be resolved and wrapped
     * @param \Exception $parent      The exception we want to wrap
     * @param string     $className   The class name of the exception type we want to wrap the parent one
     *
     * @return \Exception the wrapped exception
     */
    public function wrapException(
        array $columnNames = array(),
        \Exception $parent = null,
        $className = '\TechDivision\Import\Exceptions\WrappedColumnException'
    ) {

        // initialize the message
        $message = $parent->getMessage();

        // query whether or not has been a result of invalid data of a previous column of a CSV file
        if ($this->hasOriginalData()) {
            // load the original data
            $originalData = $this->getOriginalData();

            // replace old filename and line number of the original message
            $message = $this->appendExceptionSuffix(
                $this->stripExceptionSuffix($message),
                $originalData[ColumnKeys::ORIGINAL_FILENAME],
                $originalData[ColumnKeys::ORIGINAL_LINE_NUMBER]
            );

        } else {
            // append filename and line number to the original message
            $message = $this->appendExceptionSuffix(
                $this->stripExceptionSuffix($message),
                $this->filename,
                $this->lineNumber
            );
        }

        // query whether or not, column names has been passed
        if (sizeof($columnNames) > 0) {
            // prepare the original column names
            $originalColumnNames = array();
            foreach ($columnNames as $columnName) {
                $originalColumnNames[] = $this->resolveOriginalColumnName($columnName);
            }

            // append the column information
            $message = sprintf('%s in column(s) %s', $message, implode(', ', $originalColumnNames));
        }

        // create a new exception and wrap the parent one
        return new $className($message, null, $parent);
    }

    /**
     * Strip's the exception suffix containing filename and line number from the
     * passed message.
     *
     * @param string $message The message to strip the exception suffix from
     *
     * @return mixed The message without the exception suffix
     */
    public function stripExceptionSuffix($message)
    {
        return str_replace($this->appendExceptionSuffix(), '', $message);
    }

    /**
     * Append's the exception suffix containing filename and line number to the
     * passed message. If no message has been passed, only the suffix will be
     * returned
     *
     * @param string|null $message    The message to append the exception suffix to
     * @param string|null $filename   The filename used to create the suffix
     * @param string|null $lineNumber The line number used to create the suffx
     *
     * @return string The message with the appended exception suffix
     */
    public function appendExceptionSuffix($message = null, $filename = null, $lineNumber = null)
    {

        // query whether or not a filename has been passed
        if ($filename === null) {
            $filename = $this->getFilename();
        }

        // query whether or not a line number has been passed
        if ($lineNumber === null) {
            $lineNumber = $this->getLineNumber();
        }

        // if no message has been passed, only return the suffix
        if ($message === null) {
            return sprintf(' in file %s on line %d', $filename, $lineNumber);
        }

        // concatenate the message with the suffix and return it
        return sprintf('%s in file %s on line %d', $message, $filename, $lineNumber);
    }

    /**
     * Raises the value for the counter with the passed key by one.
     *
     * @param mixed $counterName The name of the counter to raise
     *
     * @return integer The counter's new value
     */
    public function raiseCounter($counterName)
    {

        // raise the counter with the passed name
        return $this->getRegistryProcessor()->raiseCounter(
            $this->getSerial(),
            $counterName
        );
    }

    /**
     * Merge the passed array into the status of the actual import.
     *
     * @param array $status The status information to be merged
     *
     * @return void
     */
    public function mergeAttributesRecursive(array $status)
    {

        // merge the passed status
        $this->getRegistryProcessor()->mergeAttributesRecursive(
            $this->getSerial(),
            $status
        );
    }
}
