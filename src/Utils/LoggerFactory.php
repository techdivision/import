<?php

/**
 * TechDivision\Import\Utils\LoggerFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Configuration\LoggerConfigurationInterface;

/**
 * Logger factory implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 15.0.0
 * @see        \TechDivision\Import\Loggers\MonologLoggerFactory
 */
class LoggerFactory
{

    /**
     * Creates a new logger instance based on the passed logger configuration.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface       $configuration       The system configuration
     * @param \TechDivision\Import\Configuration\LoggerConfigurationInterface $loggerConfiguration The logger configuration
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     */
    public static function factory(
        ConfigurationInterface $configuration,
        LoggerConfigurationInterface $loggerConfiguration
    ) {

        // load the available processors from the configuration
        $availableProcessors = $loggerConfiguration->getProcessors();

        // initialize the processors
        $processors = array();
        /** @var \TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface $processorConfiguration */
        foreach ($availableProcessors as $processorConfiguration) {
            $reflectionClass = new \ReflectionClass($processorConfiguration->getType());
            $processors[] = $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $processorConfiguration->getParams()));
        }

        // load the available handlers from the configuration
        $availableHandlers = $loggerConfiguration->getHandlers();

        // initialize the handlers
        $handlers = array();
        /** @var \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration */
        foreach ($availableHandlers as $handlerConfiguration) {
            // query whether or not, we've a swift mailer configuration
            if ($swiftMailerConfiguration = $handlerConfiguration->getSwiftMailer()) {
                // load the factory that creates the swift mailer instance
                $factory = $swiftMailerConfiguration->getFactory();
                // create the swift mailer instance
                $swiftMailer = $factory::factory($swiftMailerConfiguration);

                // load the generic logger configuration
                $bubble = $handlerConfiguration->getParam(LoggerKeys::BUBBLE);
                $logLevel = $handlerConfiguration->getParam(LoggerKeys::LOG_LEVEL);

                // load sender/receiver configuration
                $to = $swiftMailerConfiguration->getParam(SwiftMailerKeys::TO);
                $from = $swiftMailerConfiguration->getParam(SwiftMailerKeys::FROM);
                $subject = $swiftMailerConfiguration->getParam(SwiftMailerKeys::SUBJECT);
                $contentType = $swiftMailerConfiguration->getParam(SwiftMailerKeys::CONTENT_TYPE);

                // initialize the message template
                $message = $swiftMailer->createMessage()
                    ->setSubject(sprintf('[%s] %s', $configuration->getSystemName(), $subject))
                    ->setFrom($from)
                    ->setTo($to)
                    ->setBody('', $contentType);

                // initialize the handler node
                $reflectionClass = new \ReflectionClass($handlerConfiguration->getType());
                $handler = $reflectionClass->newInstanceArgs(array($swiftMailer, $message, $logLevel, $bubble));
            } else {
                // initialize the handler node
                $reflectionClass = new \ReflectionClass($handlerConfiguration->getType());

                // load the params
                $params = $handlerConfiguration->getParams();

                // set the default log level, if not already set explicitly
                if (!isset($params[ConfigurationKeys::LEVEL])) {
                    $params[ConfigurationKeys::LEVEL] = $configuration->getLogLevel();
                }

                // create the handler instance
                $handler = $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $params));
            }

            // if we've a formatter, initialize the formatter also
            if ($formatterConfiguration = $handlerConfiguration->getFormatter()) {
                $reflectionClass = new \ReflectionClass($formatterConfiguration->getType());
                $handler->setFormatter($reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $formatterConfiguration->getParams())));
            }

            // add the handler
            $handlers[] = $handler;
        }

        // prepare the logger params
        $loggerParams = array(
            ConfigurationKeys::NAME       => $loggerConfiguration->getChannelName(),
            ConfigurationKeys::HANDLERS   => $handlers,
            ConfigurationKeys::PROCESSORS => $processors
        );

        // append the params from the logger configuration
        $loggerParams = array_merge($loggerParams, $loggerConfiguration->getParams());

        // initialize the logger instance itself
        $reflectionClass = new \ReflectionClass($loggerConfiguration->getType());
        return $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $loggerParams));
    }
}
