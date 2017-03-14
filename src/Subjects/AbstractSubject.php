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

use Psr\Log\LoggerInterface;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Interpreter;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Callbacks\CallbackVisitor;
use TechDivision\Import\Callbacks\CallbackInterface;
use TechDivision\Import\Observers\ObserverVisitor;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Utils\LoggerKeys;

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
     * The system configuration.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    protected $configuration;

    /**
     * The array with the system logger instances.
     *
     * @var array
     */
    protected $systemLoggers = array();

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
     * The name of the file to be imported.
     *
     * @var string
     */
    protected $filename;

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
     * Contain's the column names from the header line.
     *
     * @var array
     */
    protected $headers = array();

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
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $configuration              The subject configuration instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface         $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface         $coreConfigDataUidGenerator The UID generator for the core config data
     * @param array                                                            $systemLoggers              The array with the system loggers instances
     */
    public function __construct(
        SubjectConfigurationInterface $configuration,
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        array $systemLoggers
    ) {
        $this->configuration = $configuration;
        $this->registryProcessor = $registryProcessor;
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
        $this->systemLoggers = $systemLoggers;
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
     * Stop's observer execution on the actual row.
     *
     * @return void
     */
    public function skipRow()
    {
        $this->skipRow = true;
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
     * Return's the actual operation name.
     *
     * @return string
     */
    public function getOperationName()
    {
        return $this->operationName;
    }

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     */
    public function getHeader($name)
    {

        // query whether or not, the header is available
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        // throw an exception, if not
        throw new \InvalidArgumentException(sprintf('Header %s is not available', $name));
    }

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     */
    public function addHeader($name)
    {

        // add the header
        $this->headers[$name] = $position = sizeof($this->headers);

        // return the new header's position
        return $position;
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
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The system configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    public function getSystemLogger($name = LoggerKeys::SYSTEM)
    {

        // query whether or not, the requested logger is available
        if (isset($this->systemLoggers[$name])) {
            return $this->systemLoggers[$name];
        }

        // throw an exception if the requested logger is NOT available
        throw new \Exception(sprintf('The requested logger \'%s\' is not available', $name));
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    public function getSystemLoggers()
    {
        return $this->systemLoggers;
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
     * Return's the initialized PDO connection.
     *
     * @return \PDO The initialized PDO connection
     */
    public function getConnection()
    {
        return $this->getProductProcessor()->getConnection();
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

        // load the global data we've prepared initially
        $this->stores = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORES];
        $this->defaultStore = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::DEFAULT_STORE];
        $this->rootCategories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ROOT_CATEGORIES];
        $this->coreConfigData = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CORE_CONFIG_DATA];

        // initialize the operation name
        $this->operationName = $this->getConfiguration()->getConfiguration()->getOperationName();

        // merge the callback mappings with the mappings from the child instance
        $this->callbackMappings = array_merge($this->callbackMappings, $this->getDefaultCallbackMappings());

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

        // initialize the callbacks/observers
        CallbackVisitor::get()->visit($this);
        ObserverVisitor::get()->visit($this);
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @return void
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the source directory for the next subject
        $registryProcessor->mergeAttributesRecursive(
            $this->getSerial(),
            array(RegistryKeys::SOURCE_DIRECTORY => $this->getNewSourceDir())
        );

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated source directory to %s', __CLASS__, $this->getNewSourceDir())
        );
    }

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @return string The new source directory
     */
    protected function getNewSourceDir()
    {
        return sprintf('%s/%s', $this->getConfiguration()->getTargetDir(), $this->getSerial());
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
     * @param string $serial   The unique process serial
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
            if (is_file($failedFilename) ||
                is_file($importedFilename) ||
                is_file($inProgressFilename)
            ) {
                // log a debug message and exit
                $systemLogger->debug(sprintf('Import running, found inProgress file %s', $inProgressFilename));
                return;
            }

            // flag file as in progress
            touch($inProgressFilename);

            // track the start time
            $startTime = microtime(true);

            // initialize serial and filename
            $this->setSerial($serial);
            $this->setFilename($filename);

            // load the system logger
            $systemLogger = $this->getSystemLogger();

            // initialize the global global data to import a bunch
            $this->setUp();

            // initialize the lexer instance itself
            $lexer = new Lexer($this->getLexerConfig());

            // initialize the interpreter
            $interpreter = new Interpreter();
            $interpreter->addObserver(array($this, 'importRow'));

            // query whether or not we want to use the strict mode
            if (!$this->getConfiguration()->isStrictMode()) {
                $interpreter->unstrict();
            }

            // log a message that the file has to be imported
            $systemLogger->debug(sprintf('Now start importing file %s', $filename));

            // parse the CSV file to be imported
            $lexer->parse($filename, $interpreter);

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // clean up the data after importing the bunch
            $this->tearDown();

            // log a message that the file has successfully been imported
            $systemLogger->debug(sprintf('Succesfully imported file %s in %f s', $filename, $endTime));

            // rename flag file, because import has been successfull
            rename($inProgressFilename, $importedFilename);

        } catch (\Exception $e) {
            // rename the flag file, because import failed and write the stack trace
            rename($inProgressFilename, $failedFilename);
            file_put_contents($failedFilename, $e->__toString());

            // clean up the data after importing the bunch
            $this->tearDown();

            // re-throw the exception
            throw $e;
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
        $pattern = sprintf('/^.*\/%s.*\\.csv$/', $this->getConfiguration()->getPrefix());

        // stop processing, if the filename doesn't match
        return (boolean) preg_match($pattern, $filename);
    }

    /**
     * Initialize and return the lexer configuration.
     *
     * @return \Goodby\CSV\Import\Standard\LexerConfig The lexer configuration
     */
    protected function getLexerConfig()
    {

        // initialize the lexer configuration
        $config = new LexerConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $this->getConfiguration()->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $this->getConfiguration()->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $this->getConfiguration()->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $this->getConfiguration()->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $this->getConfiguration()->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // return the lexer configuratio
        return $config;
    }

    /**
     * Imports the passed row into the database.
     *
     * If the import failed, the exception will be catched and logged,
     * but the import process will be continued.
     *
     * @param array $row The row with the data to be imported
     *
     * @return void
     */
    public function importRow(array $row)
    {

        // raise the line number and reset the skip row flag
        $this->lineNumber++;
        $this->skipRow = false;

        // initialize the headers with the columns from the first line
        if (sizeof($this->headers) === 0) {
            foreach ($row as $value => $key) {
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
                    break;
                }
                // if not, process the next observer
                if ($observer instanceof ObserverInterface) {
                    $row = $observer->handle($row);
                }
            }
        }

        // log a debug message with the actual line nr/file information
        $this->getSystemLogger()->debug(
            sprintf(
                'Successfully processed row (operation: %s) in file %s on line %d',
                $this->operationName,
                $this->filename,
                $this->lineNumber
            )
        );
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    public function mapAttributeCodeByHeaderMapping($attributeCode)
    {

        // load the header mappings
        $headerMappings = $this->getHeaderMappings();

        // query weather or not we've a mapping, if yes, map the attribute code
        if (isset($headerMappings[$attributeCode])) {
            $attributeCode = $headerMappings[$attributeCode];
        }

        // return the (mapped) attribute code
        return $attributeCode;
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
        if ($this->storeViewCode != null) {
            return $this->storeViewCode;
        }

        // if NOT and a default code is available
        if ($default != null) {
            // return the default value
            return $default;
        }
    }

    /**
     * Return's the root category for the actual view store.
     *
     * @return array The store's root category
     * @throws \Exception Is thrown if the root category for the passed store code is NOT available
     */
    public function getRootCategory()
    {

        // load the default store
        $defaultStore = $this->getDefaultStore();

        // load the actual store view code
        $storeViewCode = $this->getStoreViewCode($defaultStore[MemberNames::CODE]);

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
        if ($scope === ScopeKeys::SCOPE_STORES && isset($this->stores[$scopeId])) {
            // replace scope with 'websites' and website ID
            $coreConfigData = array_merge(
                $coreConfigData,
                array(
                    MemberNames::SCOPE    => ScopeKeys::SCOPE_WEBSITES,
                    MemberNames::SCOPE_ID => $this->stores[$scopeId][MemberNames::WEBSITE_ID]
                )
            );

            // generate the UID from the passed data, merged with the 'websites' scope and ID
            $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

            // query whether or not, the configuration value on 'websites' level
            if (isset($this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE])) {
                return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE];
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
}
