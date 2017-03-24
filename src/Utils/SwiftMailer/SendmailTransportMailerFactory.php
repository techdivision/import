<?php

/**
 * TechDivision\Import\Utils\SwiftMailer\SendmailTransportMailerFactory
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

namespace TechDivision\Import\Utils\SwiftMailer;

use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\Configuration\SwiftMailerConfigurationInterface;

/**
 * Factory implementation for a swift mailer with a simple sendmail transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SendmailTransportMailerFactory
{

    /**
     * Creates a new swift mailer instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SwiftMailerConfigurationInterface $swiftMailerConfiguration The mailer configuration
     *
     * @return \Swift_Mailer The mailer instance
     */
    public static function factory(SwiftMailerConfigurationInterface $swiftMailerConfiguration)
    {

        // load the transport configuration
        $transportConfiguration = $swiftMailerConfiguration->getTransport();

        // load the transport factory
        $transportFactory = $transportConfiguration->getTransportFactory();

        // initialize and load the sendmail command parameter
        $command = '/usr/sbin/sendmail -bs';
        if ($transportConfiguration->hasParam(SwiftMailerKeys::COMMAND)) {
            $command = $transportConfiguration->getParam(SwiftMailerKeys::COMMAND);
        }

        // initialize and create the mailer transport instance
        $transport = $transportFactory::newInstance($command);

        // load the mailer factory
        $mailerFactory = $swiftMailerConfiguration->getMailerFactory();

        // initialize, create and return the swift mailer instance
        return $mailerFactory::newInstance($transport);
    }
}
