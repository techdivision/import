<?php

/**
 * TechDivision\Import\Loggers\StreamHandlerFactory
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers;

use Ramsey\Uuid\Uuid;
use Monolog\Handler\StreamHandler;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * Handler factory implementation for a stream handler that changes the log
 * filename depending on the target directory of the actual import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StreamHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The log level to use.
     *
     * @var string
     */
    protected $defaultLogLevel;

    /**
     * The target directory to place the log file within.
     *
     * @var string
     */
    protected $targetDirectory;

    /**
     * The actual log filename.
     *
     * @var string
     */
    protected $stream;

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initialize the processor with the actual configuration and registry processor instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The actual configuration instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     */
    public function __construct(ConfigurationInterface $configuration, RegistryProcessorInterface $registryProcessor)
    {

        // load the default values for the log level and the target directory from the configuration
        $this->defaultLogLevel = $configuration->getLogLevel();
        $this->targetDirectory = $configuration->getTargetDir();

        // set the registry processor instance
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration
     *
     * @return \Monolog\Handler\HandlerInterface The handler instance
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {

        // initialize the status array
        $status = array();

        // try to load the actual import status from the registry processor
        if ($this->registryProcessor->hasAttribute(CacheKeys::STATUS)) {
            $status = $this->registryProcessor->getAttribute(CacheKeys::STATUS);
        }

        // query whether or not the target directory has been changed
        if (isset($status[RegistryKeys::TARGET_DIRECTORY])) {
            $this->targetDirectory = $status[RegistryKeys::TARGET_DIRECTORY];
        }

        // prepare the log filename
        $stream = $handlerConfiguration->getParam(ConfigurationKeys::STREAM);

        // query whether or not the given log file is relative to the target
        // directory (for backwards compatility the default value must be YES)
        if ($handlerConfiguration->hasParam(ConfigurationKeys::RELATIVE) ? $handlerConfiguration->getParam(ConfigurationKeys::RELATIVE) : true) {
            $stream = implode(DIRECTORY_SEPARATOR, array($this->targetDirectory, $stream));
        }

        // override the filename in the params
        $params = array_replace($handlerConfiguration->getParams(), array(ConfigurationKeys::STREAM => $stream));

        // override the log level, if specified in
        // the configuration or as CLI option
        if ($this->defaultLogLevel) {
            $params[ConfigurationKeys::LEVEL] = $this->defaultLogLevel;
        }

        // query wehther or not the log filename has been changed, if yes rename it
        if ($this->stream && is_file($this->stream) && $this->stream !== $stream) {
            rename($this->stream, $stream);
        }

        // set the new log filename
        $this->stream = $stream;

        // we've to query whether or not the status has been initialized
        if (isset($status[RegistryKeys::FILES])) {
            // if yes, merge the log filename into the status
            $status = array_merge_recursive(
                $status,
                array(
                    RegistryKeys::FILES => array(
                        $this->stream => array(
                            Uuid::uuid4()->toString() => array(
                                RegistryKeys::STATUS => 1,
                                RegistryKeys::PROCESSED_ROWS => 0
                            )
                        )
                    )
                )
            );

            // add the logger to the import artefacts
            $this->registryProcessor->mergeAttributesRecursive(CacheKeys::STATUS, $status);
        }

        // create and return the handler instance
        $reflectionClass = new \ReflectionClass(StreamHandler::class);
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $params));
    }
}
