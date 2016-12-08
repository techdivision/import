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
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Callbacks\CallbackInterface;
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
     * The virtual filesystem instance.
     *
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

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

        // initialize the filesystems root directory
        $this->setRootDir(
            $this->getConfiguration()->getParam(ConfigurationKeys::ROOT_DIRECTORY, getcwd())
        );

        // initialize the filesystem
        $this->setFilesystem(new Filesystem(new Local($this->getRootDir())));
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
            $systemLogger->debug(sprintf('Now start importing file %s', $filename));

            // parse the CSV file to be imported
            $lexer->parse($filename, $interpreter);

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file has successfully been imported
            $systemLogger->debug(sprintf('Succesfully imported file %s in %f s', $filename, $endTime));

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
