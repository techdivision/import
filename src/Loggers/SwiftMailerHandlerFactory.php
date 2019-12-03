<?php

/**
 * TechDivision\Import\Loggers\SwiftMailerHandlerFactory
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

use Monolog\Handler\SwiftMailerHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;
use TechDivision\Import\Loggers\SwiftMailer\TransportMailerFactoryInterface;

/**
 * Swift Mailer Handler factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SwiftMailerHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The system name to use.
     *
     * @var string
     */
    protected $systemName;

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Initialize the processor with the actual configuration instance
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The actual configuration instance
     */
    public function __construct(ContainerInterface $container, ConfigurationInterface $configuration)
    {
        $this->container = $container;
        $this->systemName = $configuration->getSystemName();
    }

    /**
     * Returns the DI container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The DI container instance
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Return's the system name to use.
     *
     * @return string The system name
     */
    protected function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $formatterConfiguration The formatter configuration
     *
     * @return object The formatter instance
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {

        // load the swift mailer configuration
        $swiftMailerConfiguration = $handlerConfiguration->getSwiftMailer();

        // create the swift mailer (factory) instance
        $possibleSwiftMailer = $this->getContainer()->get($swiftMailerConfiguration->getId());

        // query whether or not we've a factory or the instance
        /** @var \Swift_Mailer $swiftMailer */
        if ($possibleSwiftMailer instanceof TransportMailerFactoryInterface) {
            $swiftMailer = $possibleSwiftMailer->factory($swiftMailerConfiguration->getTransport());
        } else {
            $swiftMailer = $possibleSwiftMailer;
        }

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
            ->setSubject(sprintf('[%s] %s', $this->getSystemName(), $subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody('', $contentType);

        // initialize the handler node
        $reflectionClass = new \ReflectionClass(SwiftMailerHandler::class);
        return $reflectionClass->newInstanceArgs(array($swiftMailer, $message, $logLevel, $bubble));
    }
}
