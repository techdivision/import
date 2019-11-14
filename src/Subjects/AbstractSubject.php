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

use Ramsey\Uuid\Uuid;
use League\Event\EmitterInterface;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\RowTrait;
use TechDivision\Import\HeaderTrait;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\EventNames;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\EntityTypeCodes;
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
abstract class AbstractSubject implements SubjectInterface, FilesystemSubjectInterface, DateConverterSubjectInterface
{

    /**
     * The trait that provides basic filesystem handling functionality.
     *
     * @var \TechDivision\Import\Subjects\FilesystemTrait
     */
    use FilesystemTrait;

    /**
     * The trait that provides basic filesystem handling functionality.
     *
     * @var \TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The trait that provides date converting functionality.
     *
     * @var \TechDivision\Import\DateConverterTrait
     */
    use DateConverterTrait;

    /**
     * The trait that provides header handling functionality.
     *
     * @var \TechDivision\Import\HeaderTrait
     */
    use HeaderTrait;

    /**
     * The trait that provides row handling functionality.
     *
     * @var \TechDivision\Import\RowTrait
     */
    use RowTrait;

    /**
     * The unique identifier for the actual invocation.
     *
     * @var string
     */
    protected $uniqueId;

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
     * The import adapter instance.
     *
     * @var \TechDivision\Import\Adapter\ImportAdapterInterface
     */
    protected $importAdapter;

    /**
     * The subject configuration.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    protected $configuration;

    /**
     * The plugin configuration.
     *
     * @var \TechDivision\Import\Configuration\PluginConfigurationInterface
     */
    protected $pluginConfiguration;

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
     * The event emitter instance.
     *
     * @var \League\Event\EmitterInterface
     */
    protected $emitter;

    /**
     * The status of the file (0 = not processed, 1 = successfully processed, 2 = processed with failure)
     *
     * @var array
     */
    protected $status = array();

    /**
     * Mapping for the virtual entity type code to the real Magento 2 EAV entity type code.
     *
     * @var array
     */
    protected $entityTypeCodeMappings = array(
        EntityTypeCodes::EAV_ATTRIBUTE                 => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::EAV_ATTRIBUTE_SET             => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_PRICE         => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => EntityTypeCodes::CATALOG_PRODUCT
    );

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \Doctrine\Common\Collections\Collection                  $systemLoggers              The array with the system loggers instances
     * @param \League\Event\EmitterInterface                           $emitter                    The event emitter instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        Collection $systemLoggers,
        EmitterInterface $emitter
    ) {
        $this->emitter = $emitter;
        $this->systemLoggers = $systemLoggers;
        $this->registryProcessor = $registryProcessor;
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
    }

    /**
     * Return's the event emitter instance.
     *
     * @return \League\Event\EmitterInterface The event emitter instance
     */
    public function getEmitter()
    {
        return $this->emitter;
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
     * Return's the default callback mappings.
     *
     * @return array The default callback mappings
     */
    public function getDefaultCallbackMappings()
    {
        return array();
    }

    /**
     * Load the default header mappings from the configuration.
     *
     * @return array
     */
    public function getDefaultHeaderMappings()
    {

        // initialize the array for the default header mappings
        $defaultHeaderMappings = array();

        // load the Magento edition and the entity type from the execution context
        $entityTypeCode = $this->getExecutionContext()->getEntityTypeCode();

        // load the header mappings from the configuration
        $headerMappings = $this->getConfiguration()->getHeaderMappings();

        // query whether or not header mappings for the entity type are available
        if (isset($headerMappings[$entityTypeCode])) {
            $defaultHeaderMappings = $headerMappings[$entityTypeCode];
        }

        // return the default header mappings
        return $defaultHeaderMappings;
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
        return $this->getDateConverter()->convert($value);
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
        return $this->getImportAdapter()->explode($value, $delimiter);
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
     * Return's the subject's execution context configuration.
     *
     * @return \TechDivision\Import\ExecutionContextInterface The execution context configuration to use
     */
    public function getExecutionContext()
    {
        return $this->getConfiguration()->getPluginConfiguration()->getExecutionContext();
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
     * Merge's the passed status into the actual one.
     *
     * @param array $status The status to MergeBuilder
     *
     * @return void
     */
    public function mergeStatus(array $status)
    {
        $this->status = array_merge_recursive($this->status, $status);
    }

    /**
     * Retur's the actual status.
     *
     * @return array The actual status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return's the unique identifier for the actual invocation.
     *
     * @return string The unique identifier
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
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

        // initialize the unique ID for the actual invocation
        $this->uniqueId = Uuid::uuid4()->toString();

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // load the global data, if prepared initially
        if (isset($status[RegistryKeys::GLOBAL_DATA])) {
            $this->stores = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORES];
            $this->defaultStore = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::DEFAULT_STORE];
            $this->storeWebsites  = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];
            $this->rootCategories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ROOT_CATEGORIES];
            $this->coreConfigData = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CORE_CONFIG_DATA];
        }

        // merge the header mappings with the values found in the configuration
        $this->headerMappings = array_merge($this->headerMappings, $this->getDefaultHeaderMappings());

        // merge the callback mappings with the mappings from the child instance
        $this->callbackMappings = array_merge($this->callbackMappings, $this->getDefaultCallbackMappings());

        // load the available callbacks from the configuration
        $availableCallbacks = $this->getConfiguration()->getCallbacks();

        // merge the callback mappings the the one from the configuration file
        foreach ($availableCallbacks as $callbackMappings) {
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
        foreach ($this->getStatus() as $key => $status) {
            $registryProcessor->mergeAttributesRecursive($key, $status);
        }

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated status data for import %s', get_class($this), $serial)
        );
    }

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    public function getTargetDir()
    {

        // load the status from the registry processor
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not a target directory (mandatory) has been configured
        if (isset($status[RegistryKeys::TARGET_DIRECTORY])) {
            return $status[RegistryKeys::TARGET_DIRECTORY];
        }

        // throw an exception if the root category is NOT available
        throw new \Exception(sprintf('Can\'t find a target directory in status data for import %s', $this->getSerial()));
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
            // initialize the serial/filename
            $this->setSerial($serial);
            $this->setFilename($filename);

            // invoke the events that has to be fired before the artfact will be processed
            $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_PROCESS_START, $this);
            $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_PROCESS_START), $this);

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

            // initialize the last time we've logged the counter with the processed rows per minute
            $this->lastLog = time();

            // log a message that the file has to be imported
            $systemLogger->info(sprintf('Now start processing file %s', basename($filename)));

            // let the adapter process the file
            $this->getImportAdapter()->import(array($this, 'importRow'), $filename);

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file has successfully been imported
            $systemLogger->info(sprintf('Successfully processed file %s with %d lines in %f s', basename($filename), $this->lineNumber, $endTime));

            // rename flag file, because import has been successfull
            if ($this->getConfiguration()->isCreatingImportedFile()) {
                $this->rename($inProgressFilename, $importedFilename);
            } else {
                $this->getFilesystemAdapter()->delete($inProgressFilename);
            }

            // update the status
            $this->mergeStatus(
                array(
                    RegistryKeys::STATUS => array(
                        RegistryKeys::FILES => array(
                            $filename => array(
                                $this->getUniqueId() => array(
                                    RegistryKeys::STATUS => 1,
                                    RegistryKeys::PROCESSED_ROWS => $this->getLineNumber()
                                )
                            )
                        )
                    )
                )
            );

            // invoke the events that has to be fired when the artfact has been successfully processed
            $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_PROCESS_SUCCESS, $this);
            $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_PROCESS_SUCCESS), $this);
        } catch (\Exception $e) {
            // rename the flag file, because import failed and write the stack trace
            $this->rename($inProgressFilename, $failedFilename);
            $this->write($failedFilename, $e->__toString());

            // update the status with the error message
            $this->mergeStatus(
                array(
                    RegistryKeys::STATUS => array(
                        RegistryKeys::FILES => array(
                            $filename => array(
                                $this->getUniqueId() => array(
                                    RegistryKeys::STATUS         => 2,
                                    RegistryKeys::ERROR_MESSAGE  => $e->getMessage(),
                                    RegistryKeys::PROCESSED_ROWS => $this->getLineNumber()
                                )
                            )
                        )
                    )
                )
            );

            // invoke the events that has to be fired when the artfact can't be processed
            $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_PROCESS_FAILURE, $this, $e);
            $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_PROCESS_FAILURE), $this, $e);

            // do not wrap the exception if not already done
            if ($e instanceof WrappedColumnException) {
                throw $e;
            }

            // else wrap and throw the exception
            throw $this->wrapException(array(), $e);
        }
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

        // invoke the events that has to be fired before the artfact's row will be processed
        $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_ROW_PROCESS_START, $this);
        $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_ROW_PROCESS_START), $this);

        // initialize the headers with the columns from the first line
        if (sizeof($this->headers) === 0) {
            // invoke the events that has to be fired before the artfact's header row will be processed
            $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_START, $this);
            $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_START), $this);
            // iterate over the column name => key an map the header names, if necessary
            foreach ($this->row as $value => $key) {
                $this->headers[$this->mapAttributeCodeByHeaderMapping($key)] = $value;
            }
            // invoke the events that has to be fired when the artfact's header row has been successfully processed
            $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_SUCCESS, $this);
            $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_SUCCESS), $this);
        } else {
            // load the available observers
            $availableObservers = $this->getObservers();

            // process the observers
            foreach ($availableObservers as $observers) {
                // invoke the pre-import/import and post-import observers
                /** @var \TechDivision\Import\Observers\ObserverInterface $observer */
                foreach ($observers as $observer) {
                    // query whether or not we have to skip the row
                    if ($this->skipRow) {
                        // log a debug message with the actual line nr/file information
                        $this->getSystemLogger()->debug(
                            $this->appendExceptionSuffix(
                                sprintf(
                                    'Skip processing operation "%s" after observer "%s"',
                                    implode(' > ', $this->getConfiguration()->getConfiguration()->getOperationNames()),
                                    get_class($observer)
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
        }

        // query whether or not a minute has been passed
        if ($this->lastLog < time() - 59) {
            // log the number processed rows per minute
            $this->getSystemLogger()->info(
                sprintf(
                    'Successfully processed "%d (%d)" rows per minute of file "%s"',
                    $this->lineNumber - $this->lastLineNumber,
                    $this->lineNumber,
                    basename($this->getFilename())
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
                    implode(' > ', $this->getConfiguration()->getConfiguration()->getOperationNames())
                )
            )
        );

        // invoke the events that has to be fired when the artfact's row has been successfully processed
        $this->getEmitter()->emit(EventNames::SUBJECT_ARTEFACT_ROW_PROCESS_SUCCESS, $this);
        $this->getEmitter()->emit($this->getEventName(EventNames::SUBJECT_ARTEFACT_ROW_PROCESS_SUCCESS), $this);
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
            RegistryKeys::COUNTERS,
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
        return $this->getRegistryProcessor()->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            $status
        );
    }

    /**
     * Return's the entity type code to be used.
     *
     * @return string The entity type code to be used
     */
    public function getEntityTypeCode()
    {

        // load the configuration specific entity type code from the plugin configuration
        $entityTypeCode = $this->getExecutionContext()->getEntityTypeCode();

        // try to map the entity type code
        if (isset($this->entityTypeCodeMappings[$entityTypeCode])) {
            $entityTypeCode = $this->entityTypeCodeMappings[$entityTypeCode];
        }

        // return the (mapped) entity type code
        return $entityTypeCode;
    }

    /**
     * Concatenates and returns the event name for the actual plugin and subject context.
     *
     * @param string $eventName The event name to concatenate
     *
     * @return string The concatenated event name
     */
    protected function getEventName($eventName)
    {
        return  sprintf(
            '%s.%s.%s',
            $this->getConfiguration()->getPluginConfiguration()->getId(),
            $this->getConfiguration()->getId(),
            $eventName
        );
    }
}
