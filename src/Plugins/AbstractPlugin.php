<?php

/**
 * TechDivision\Import\Plugins\AbstractPlugin
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Adapter\ImportAdapterInterface;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Loggers\SwiftMailer\TransportMailerFactoryInterface;

/**
 * Abstract plugin implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractPlugin implements PluginInterface
{

    /**
     * The application instance.
     *
     * @var \TechDivision\Import\ApplicationInterface
     */
    protected $application;

    /**
     * The plugin configuration instance.
     *
     * @var \TechDivision\Import\Configuration\PluginConfigurationInterface
     */
    protected $pluginConfiguration;

    /**
     * The import adapter instance.
     *
     * @var \TechDivision\Import\Adapter\ImportAdapterInterface
     */
    protected $importAdapter;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     *  Set's the plugin configuration instance.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration instance
     *
     * @return void
     */
    public function setPluginConfiguration(PluginConfigurationInterface $pluginConfiguration)
    {
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * Return's the plugin configuration instance.
     *
     * @return \TechDivision\Import\Configuration\PluginConfigurationInterface The plugin configuration instance
     */
    public function getPluginConfiguration()
    {
        return $this->pluginConfiguration;
    }

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial()
    {
        return $this->getApplication()->getSerial();
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
     * Return's the plugin's execution context configuration.
     *
     * @return \TechDivision\Import\Configuration\ExecutionContextInterface The execution context configuration to use
     */
    public function getExecutionContext()
    {
        return $this->getPluginConfiguration()->getExecutionContext();
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
     * Return's the application instance.
     *
     * @return \TechDivision\Import\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->getApplication()->getRegistryProcessor();
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    protected function getImportProcessor()
    {
        return $this->getApplication()->getImportProcessor();
    }

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    protected function getSystemLogger($name = LoggerKeys::SYSTEM)
    {
        return $this->getApplication()->getSystemLogger($name);
    }

    /**
     * Query whether or not the system logger with the passed name is available.
     *
     * @param string $name The name of the requested system logger
     *
     * @return boolean TRUE if the logger with the passed name exists, else FALSE
     */
    protected function hasSystemLogger($name = LoggerKeys::SYSTEM)
    {
        return $this->getApplication()->hasSystemLogger($name);
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    protected function getSystemLoggers()
    {
        return $this->getApplication()->getSystemLoggers();
    }

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    protected function removeLineFromFile($line, $filename)
    {
        $this->getApplication()->removeLineFromFile($line, $filename);
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The system configuration
     */
    protected function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }

    /**
     * Return's the PID filename to use.
     *
     * @return string The PID filename
     */
    protected function getPidFilename()
    {
        return $this->getConfiguration()->getPidFilename();
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    protected function getSourceDir()
    {
        return $this->getConfiguration()->getSourceDir();
    }

    /**
     * Removes the passed directory recursively.
     *
     * @param string $src Name of the directory to remove
     *
     * @return void
     * @throws \Exception Is thrown, if the directory can not be removed
     */
    protected function removeDir($src)
    {

        // open the directory
        $dir = opendir($src);

        // remove files/folders recursively
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    $this->removeDir($full);
                } else {
                    if (!unlink($full)) {
                        throw new \Exception(sprintf('Can\'t remove file %s', $full));
                    }
                }
            }
        }

        // close handle and remove directory itself
        closedir($dir);
        if (!rmdir($src)) {
            throw new \Exception(sprintf('Can\'t remove directory %s', $src));
        }
    }

    /**
     * Return's the configured swift mailer instance.
     *
     * @return \Swift_Mailer|null The mailer instance
     */
    protected function getSwiftMailer()
    {

        // the swift mailer configuration
        if ($swiftMailerConfiguration = $this->getPluginConfiguration()->getSwiftMailer()) {
            // create the swift mailer (factory) instance
            $possibleSwiftMailer = $this->getApplication()->getContainer()->get($swiftMailerConfiguration->getId());

            // query whether or not we've a factory or the instance
            /** @var \Swift_Mailer $swiftMailer */
            if ($possibleSwiftMailer instanceof TransportMailerFactoryInterface) {
                return $possibleSwiftMailer->factory($swiftMailerConfiguration->getTransport());
            }

            if ($possibleSwiftMailer instanceof \Swift_Mailer) {
                return $possibleSwiftMailer;
            }
        }

        // throw an exception if the configuration contains an invalid value
        throw new \Exception('Can\'t create SwiftMailer from configuration');
    }
}
