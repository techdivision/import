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
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FilesystemInterface;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Interpreter;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Callbacks\CallbackVisitor;
use TechDivision\Import\Callbacks\CallbackInterface;
use TechDivision\Import\Observers\ObserverVisitor;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectInterface as SubjectConfigurationInterface;

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
     * The root directory for the virtual filesystem.
     *
     * @var string
     */
    protected $rootDir;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\Configuration\SubjectInterface
     */
    protected $configuration;

    /**
     * The system logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

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
     * The virtual filesystem instance.
     *
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

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
     * Mappings for attribute code => CSV column header.
     *
     * @var array
     */
    protected $headerMappings = array(
        'product_online' => 'status',
        'tax_class_name' => 'tax_class_id',
        'bundle_price_type' => 'price_type',
        'bundle_sku_type' => 'sku_type',
        'bundle_price_view' => 'price_view',
        'bundle_weight_type' => 'weight_type',
        'base_image' => 'image',
        'base_image_label' => 'image_label',
        'thumbnail_image' => 'thumbnail',
        'thumbnail_image_label'=> 'thumbnail_label',
        'bundle_shipment_type' => 'shipment_type'
    );

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
     * Set's the system configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject $configuration The system configuration
     *
     * @return void
     */
    public function setConfiguration(SubjectConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\Configuration\SubjectInterface The system configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
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
     * Set's root directory for the virtual filesystem.
     *
     * @param string $rootDir The root directory for the virtual filesystem
     *
     * @return void
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Return's the root directory for the virtual filesystem.
     *
     * @return string The root directory for the virtual filesystem
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * Set's the virtual filesystem instance.
     *
     * @param \League\Flysystem\FilesystemInterface $filesystem The filesystem instance
     *
     * @return void
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Return's the virtual filesystem instance.
     *
     * @return \League\Flysystem\FilesystemInterface The filesystem instance
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Sets's the RegistryProcessor instance to handle the running threads.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     *
     * @return void
     */
    public function setRegistryProcessor(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
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

        // initialize the filesystems root directory
        $this->setRootDir(
            $this->getConfiguration()->getParam(ConfigurationKeys::ROOT_DIRECTORY, getcwd())
        );

        // initialize the filesystem
        $this->filesystem = new Filesystem(new Local($this->getRootDir()));

        // initialize the operation name
        $this->operationName = $this->getConfiguration()->getConfiguration()->getOperationName();

        // initialize the callbacks/observers
        CallbackVisitor::get()->visit($this);
        ObserverVisitor::get()->visit($this);
    }

    /**
     * This method tries to resolve the passed path and returns it. If the path
     * is relative, the actual working directory will be prepended.
     *
     * @param string $path The path to be resolved
     *
     * @return string The resolved path
     * @throws \InvalidArgumentException Is thrown, if the path can not be resolved
     */
    public function resolvePath($path)
    {
        // if we've an absolute path, return it immediately
        if ($this->getFilesystem()->has($path)) {
            return $path;
        }

        // try to prepend the actual working directory, assuming we've a relative path
        if ($this->getFilesystem()->has($path = getcwd() . DIRECTORY_SEPARATOR . $path)) {
            return $path;
        }

        // throw an exception if the passed directory doesn't exists
        throw new \InvalidArgumentException(
            sprintf('Directory %s doesn\'t exist', $path)
        );
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

        // query weather or not we've a mapping, if yes, map the attribute code
        if (isset($this->headerMappings[$attributeCode])) {
            $attributeCode = $this->headerMappings[$attributeCode];
        }

        // return the (mapped) attribute code
        return $attributeCode;
    }
}
