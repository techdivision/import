<?php

/**
 * TechDivision\Import\ApplicationInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

use Psr\Container\ContainerInterface;
use TechDivision\Import\Utils\LoggerKeys;

/**
 * The interface for a M2IF application implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ApplicationInterface extends ContainerInterface
{

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial();

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
     * Query whether or not the system logger with the passed name is available.
     *
     * @param string $name The name of the requested system logger
     *
     * @return boolean TRUE if the logger with the passed name exists, else FALSE
     */
    public function hasSystemLogger($name = LoggerKeys::SYSTEM);

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The array with the system logger instances
     */
    public function getSystemLoggers();

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    public function getRegistryProcessor();

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    public function getImportProcessor();

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The system configuration
     */
    public function getConfiguration();

    /**
     * Return's the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\TaggedContainerInterface The container instance
     */
    public function getContainer();

    /**
     * Simple method that writes the passed method the the console and the
     * system logger, if configured and a log level has been passed.
     *
     * @param string $msg      The message to log
     * @param string $logLevel The log level to use
     *
     * @return void
     */
    public function log($msg, $logLevel = null);

    /**
     * Persist the UUID of the actual import process to the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be added
     */
    public function lock();

    /**
     * Remove's the UUID of the actual import process from the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be removed
     */
    public function unlock();

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    public function removeLineFromFile($line, $filename);

    /**
     * Process the given operation.
     *
     * @param string $serial The unique serial of the actual import process
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \Exception Is thrown if the operation can't be finished successfully
     */
    public function process($serial);

    /**
     * Stop processing the operation immediately and should return an exit code > 0.
     *
     * This will stop the operation with an error output and rolling back the single transaction,
     * if it has been started by the CLI parameter `--single-transaction=true`.
     *
     * This method should be used when the import process should be interrupted in case of an
     * error and to signal the user that something went wrong.
     *
     * @param string $reason   The reason why the operation has been stopped
     * @param int    $exitCode The exit code to use, defaults to 1
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\ApplicationStoppedException Is thrown if the application has been stopped
     */
    public function stop($reason, $exitCode = 1);

    /**
     * Finish processing the operation immediately and should return an exit code 0.
     *
     * This will stop the operation without an error output and commits the single transaction,
     * if it has been started by the CLI parameter `--single-transaction=true`.
     *
     * This method should be used when the import process should be interrupted in case
     * further processing makes no sense or is not necessary and to signal the user that
     * everything is as expected.
     *
     *
     * @param string $reason   The reason why the operation has been finish
     * @param int    $exitCode The exit code to use
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\ApplicationFinishedException Is thrown if the application has been finish
     */
    public function finish($reason, $exitCode = 0);

    /**
     * Return's TRUE if the operation has been stopped, else FALSE.
     *
     * @return boolean TRUE if the process has been stopped, else FALSE
     */
    public function isStopped();

    /**
     * Returns the actual application version.
     *
     * @return string The application's version
     */
    public function getVersion();

    /**
     * Returns the actual application name.
     *
     * @return string The application's name
     */
    public function getName();

    /**
     * @param string $message   The reason why the operation has been missed
     * @param int    $exitCode The exit code to use
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\MissingFileException Is thrown if the file has been missed
     */
    public function missingFile($message, $exitCode);

    /**
     * @param string $message  The message when the import data has been invalid
     * @param int    $exitCode The exit code to use, defaults to 1
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\InvalidDataException Is thrown if the application has been stopped
     */
    public function invalidDataNoStrict($message, $exitCode);
}
