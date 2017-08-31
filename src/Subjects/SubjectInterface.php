<?php

/**
 * TechDivision\Import\Subjects\SubjectInterface
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

use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Callbacks\CallbackInterface;
use TechDivision\Import\Adapter\ImportAdapterInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * The interface for all subject implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface SubjectInterface
{

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial);

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial);

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    public function getSystemLogger($name = LoggerKeys::SYSTEM);

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    public function getSystemLoggers();

    /**
     * Return's the header mappings for the actual entity.
     *
     * @return array The header mappings
     */
    public function getHeaderMappings();

    /**
     * Return's the default callback mappings.
     *
     * @return array The default callback mappings
     */
    public function getDefaultCallbackMappings();

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat();

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    public function getMultipleFieldDelimiter();

    /**
     * Return's the callback mappings for this subject.
     *
     * @return array The array with the subject's callback mappings
     */
    public function getCallbackMappings();

    /**
     * Imports the content of the file with the passed filename.
     *
     *
     * @param string $serial   The serial of the actual import
     * @param string $filename The filename to process
     *
     * @return void
     */
    public function import($serial, $filename);

    /**
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    public function isOkFileNeeded();

    /**
     * Return's the default store.
     *
     * @return array The default store
     */
    public function getDefaultStore();

    /**
     * Return's the default store view code.
     *
     * @return array The default store view code
     */
    public function getDefaultStoreViewCode();

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
    public function getCoreConfigData($path, $default = null, $scope = ScopeKeys::SCOPE_DEFAULT, $scopeId = 0);

    /**
     * Set's the subject configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $configuration The subject configuration
     *
     * @return void
     */
    public function setConfiguration(SubjectConfigurationInterface $configuration);

    /**
     * Return's the subject configuration.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getConfiguration();

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    public function getTargetDir();

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @param string $serial The serial of the actual import
     *
     * @return string The new source directory
     */
    public function getNewSourceDir($serial);

    /**
     * Set's the import adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ImportAdapterInterface $importAdapter The import adapter instance
     *
     * @return void
     */
    public function setImportAdapter(ImportAdapterInterface $importAdapter);

    /**
     * Return's the import adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ImportAdapterInterface The import adapter instance
     */
    public function getImportAdapter();

    /**
     * Set's the virtual filesystem instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter);

    /**
     * Return's the filesystem adapater instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function getFilesystemAdapter();

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     */
    public function hasHeader($name);

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * @throws \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     */
    public function getHeader($name);

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     */
    public function addHeader($name);

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     */
    public function getHeaders();

    /**
     * Stop's observer execution on the actual row.
     *
     * @return void
     */
    public function skipRow();

    /**
     * Return's the actual row.
     *
     * @return array The actual row
     */
    public function getRow();

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string      $value     The value to extract
     * @param string|null $delimiter The delimiter used to extrace the elements
     *
     * @return array The exploded values
     */
    public function explode($value, $delimiter = null);

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    public function isDebugMode();

    /**
     * Set's the unique serial for this import process.
     *
     * @param string $serial The unique serial
     *
     * @return void
     */
    public function setSerial($serial);

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial();

    /**
     * Set's the name of the file to import
     *
     * @param string $filename The filename
     *
     * @return void
     */
    public function setFilename($filename);

    /**
     * Return's the name of the file to import.
     *
     * @return string The filename
     */
    public function getFilename();

    /**
     * Set's the actual line number.
     *
     * @param integer $lineNumber The line number
     *
     * @return void
     */
    public function setLineNumber($lineNumber);

    /**
     * Return's the actual line number.
     *
     * @return integer The line number
     */
    public function getLineNumber();

    /**
     * Prepare's the store view code in the subject.
     *
     * @return void
     */
    public function prepareStoreViewCode();

    /**
     * Return's the array with callbacks for the passed type.
     *
     * @param string $type The type of the callbacks to return
     *
     * @return array The callbacks
     */
    public function getCallbacksByType($type);

    /**
     * Register the passed observer with the specific type.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer to register
     * @param string                                           $type     The type to register the observer with
     *
     * @return void
     */
    public function registerObserver(ObserverInterface $observer, $type);

    /**
     * Register the passed callback with the specific type.
     *
     * @param \TechDivision\Import\Callbacks\CallbackInterface $callback The subject to register the callbacks for
     * @param string                                           $type     The type to register the callback with
     *
     * @return void
     */
    public function registerCallback(CallbackInterface $callback, $type);

    /**
     * Resolve's the value with the passed colum name from the actual row. If a callback will
     * be passed, the callback will be invoked with the found value as parameter. If
     * the value is NULL or empty, the default value will be returned.
     *
     * @param string        $name     The name of the column to return the value for
     * @param mixed|null    $default  The default value, that has to be returned, if the row's value is empty
     * @param callable|null $callback The callback that has to be invoked on the value, e. g. to format it
     *
     * @return mixed|null The, almost formatted, value
     */
    public function getValue($name, $default = null, callable $callback = null);

    /**
     * Return's the store view code the create the product/attributes for.
     *
     * @param string|null $default The default value to return, if the store view code has not been set
     *
     * @return string The store view code
     */
    public function getStoreViewCode($default = null);

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
    public function appendExceptionSuffix($message = null, $filename = null, $lineNumber = null);
}
