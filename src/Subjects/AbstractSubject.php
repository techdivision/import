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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Subjects;

use Psr\Log\LoggerInterface;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Interpreter;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectInterface As SubjectConfigurationInterface;

/**
 * An abstract action implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
abstract class AbstractSubject implements SubjectInterface
{

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
     * Contain's the column names from the header line.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
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
     * Set's the system configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject $configuration The system configuration
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
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->getConfiguration()->getSourceDateFormat();
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

        // prepare the observers
        foreach ($this->getConfiguration()->getObservers() as $observers) {
            $this->prepareObservers($observers);
        }

        // prepare the callbacks
        foreach ($this->getConfiguration()->getCallbacks() as $callbacks) {
            $this->prepareCallbacks($callbacks);
        }
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Prepare the observers defined in the system configuration.
     *
     * @param array  $observers The array with the observers
     * @param string $type      The actual observer type
     *
     * @return void
     */
    public function prepareObservers(array $observers, $type = null)
    {

        // iterate over the array with observers and prepare them
        foreach ($observers as $key => $observer) {
            // we have to initialize the type only on the first level
            if ($type == null) {
                $type = $key;
            }

            // query whether or not we've an subarry or not
            if (is_array($observer)) {
                $this->prepareObservers($observer, $type);
            } else {
                $this->registerObservers($type, $observer);
            }
        }
    }

    /**
     * Register the passed class name as observer with the specific type and key.
     *
     * @param string $type      The observer type to register the observer with
     * @param string $className The observer class name
     *
     * @return void
     */
    public function registerObservers($type, $className)
    {

        // query whether or not the array with the callbacks for the
        // passed type has already been initialized, or not
        if (!isset($this->observers[$type])) {
            $this->observers[$type] = array();
        }

        // append the callback with the instance of the passed type
        $this->observers[$type][] = $this->observerFactory($className);
    }

    /**
     * Initialize and return a new observer of the passed type.
     *
     * @param string $className The type of the observer to instanciate
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function observerFactory($className)
    {
        return new $className($this);
    }

    /**
     * Prepare the callbacks defined in the system configuration.
     *
     * @param array  $callbacks The array with the callbacks
     * @param string $type      The actual callback type
     *
     * @return void
     */
    public function prepareCallbacks(array $callbacks, $type = null)
    {

        // iterate over the array with callbacks and prepare them
        foreach ($callbacks as $key => $callback) {
            // we have to initialize the type only on the first level
            if ($type == null) {
                $type = $key;
            }

            // query whether or not we've an subarry or not
            if (is_array($callback)) {
                $this->prepareCallbacks($callback, $type);
            } else {
                $this->registerCallback($type, $callback);
            }
        }
    }

    /**
     * Register the passed class name as callback with the specific type and key.
     *
     * @param string $type      The callback type to register the callback with
     * @param string $className The callback class name
     *
     * @return void
     */
    public function registerCallback($type, $className)
    {

        // query whether or not the array with the callbacks for the
        // passed type has already been initialized, or not
        if (!isset($this->callbacks[$type])) {
            $this->callbacks[$type] = array();
        }

        // append the callback with the instance of the passed type
        $this->callbacks[$type][] = $this->callbackFactory($className);
    }

    /**
     * Initialize and return a new callback of the passed type.
     *
     * @param string $className The type of the callback to instanciate
     *
     * @return \TechDivision\Import\Callbacks\CallbackInterface The callback instance
     */
    public function callbackFactory($className)
    {
        return new $className($this);
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
     * Imports the content of the file with the passed filename.
     *
     * @param string $serial   The unique process serial
     * @param string $filename The filename to process
     *
     * @return void
     */
    public function import($serial, $filename)
    {

        try {
            // track the start time
            $startTime = microtime(true);

            // initialize serial and file UID
            $this->setSerial($serial);

            // load the system logger
            $systemLogger = $this->getSystemLogger();

            // initialize the global global data to import a bunch
            $this->setUp();

            // initialize the lexer configuration
            $config = new LexerConfig();
            $config->setToCharset('UTF-8');
            $config->setFromCharset('UTF-8');

            // initialize the lexer itself
            $lexer = new Lexer($config);

            // initialize the interpreter
            $interpreter = new Interpreter();
            $interpreter->addObserver(array($this, 'importRow'));

            // log a message that the file has to be imported
            $systemLogger->info(sprintf('Now start importing file %s', $filename));

            // parse the CSV file to be imported
            $lexer->parse($filename, $interpreter);

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file has successfully been imported
            $systemLogger->info(sprintf('Succesfully imported file %s in %f s', $filename, $endTime));

        } catch (\Exception $e) {
            // log a message with the stack trace
            $systemLogger->error($e->__toString());

            // re-throw the exception
            throw $e;
        }

        // clean up the data after importing the bunch
        $this->tearDown();
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

        // initialize the headers with the columns from the first line
        if (sizeof($this->getHeaders()) === 0) {
            $this->setHeaders(array_flip($row));
            return;
        }

        // process the observers
        foreach ($this->getObservers() as $observers) {
            // invoke the pre-import/import and post-import observers
            foreach ($observers as $observer) {
                if ($observer instanceof ObserverInterface) {
                    $row = $observer->handle($row);
                }
            }
        }
    }
}
