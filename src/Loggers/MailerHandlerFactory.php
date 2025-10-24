<?php

/**
 * TechDivision\Import\Loggers\MailerHandlerFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers;

use Exception;
use Monolog\Handler\SymfonyMailerHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;
use TechDivision\Import\Loggers\Mailer\TransportMailerFactoryInterface;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\MailerKeys;

/**
 * Mailer Handler factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MailerHandlerFactory implements HandlerFactoryInterface
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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container     The DI container instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The actual configuration instance
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
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration
     *
     * @return \Monolog\Handler\HandlerInterface The handler instance
     * @throws Exception
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {
        // load the mailer configuration
        $mailerConfiguration = $handlerConfiguration->getMailer();

        // create the mailer (factory) instance
        $possibleMailer = $this->getContainer()->get($mailerConfiguration->getId());

        // resolve to a MailerInterface
        if ($possibleMailer instanceof TransportMailerFactoryInterface) {
            /** @var MailerInterface $mailer */
            $mailer = $possibleMailer->factory($mailerConfiguration->getTransport());
        } else {
            $mailer = $possibleMailer;
        }

        // load the generic logger configuration
        $bubble = $handlerConfiguration->getParam(LoggerKeys::BUBBLE);
        $logLevel = $handlerConfiguration->getParam(LoggerKeys::LOG_LEVEL);

        // load sender/receiver configuration
        $to = $mailerConfiguration->getParam(MailerKeys::TO);
        $from = $mailerConfiguration->getParam(MailerKeys::FROM);
        $subject = $mailerConfiguration->getParam(MailerKeys::SUBJECT);
        $contentType = $mailerConfiguration->getParam(MailerKeys::CONTENT_TYPE);

        // initialize the message template for Symfony Mailer
        $email = (new Email())
            ->subject(sprintf('[%s] %s', $this->getSystemName(), $subject))
            ->from($from)
            ->to(...(array) $to);

        // initialize body placeholder to match configured content type
        if ($contentType === 'text/html') {
            $email->html('');
        } else {
            $email->text('');
        }

        // initialize and return the handler
        return new SymfonyMailerHandler($mailer, $email, $logLevel, $bubble);
    }
}
