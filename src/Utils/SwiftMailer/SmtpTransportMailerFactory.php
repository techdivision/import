<?php

/**
 * TechDivision\Import\Utils\SwiftMailer\SmtpTransportMailerFactory
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
 * Factory implementation for a swift mailer with SMTP transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SmtpTransportMailerFactory
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

        // load the SMTP host from the configuration
        $smtpHost = null;
        if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_HOST)) {
            $smtpHost = $transportConfiguration->getParam(SwiftMailerKeys::SMTP_HOST);
        }

        // load the SMTP port from the configuration
        $smtpPort = null;
        if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_PORT)) {
            $smtpPort = $transportConfiguration->getParam(SwiftMailerKeys::SMTP_PORT);
        }

        // load the SMTP security from the configuration
        $smtpSecurity = null;
        if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_SECURITY)) {
            $smtpSecurity = $transportConfiguration->getParam(SwiftMailerKeys::SMTP_SECURITY);
        }

        // initialize and create the mailer transport instance
        $transport = $transportFactory::newInstance($smtpHost, $smtpPort, $smtpSecurity);

        // query whether or not if a SMTP authentication mode has been specified
        if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_AUTH_MODE)) {
            // load the authentication mode from the configuration
            $transport->setAuthMode($transportConfiguration->getParam(SwiftMailerKeys::SMTP_AUTH_MODE));
            // set the stream context options, e. g. to allow self signed certificates
            $transport->setStreamOptions(
                array(
                    'ssl' => array(
                        'allow_self_signed' => true,
                        'verify_peer'       => false
                    )
                )
            );

            // load the SMTP username from the configuration
            if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_USERNAME)) {
                $transport->setUsername($transportConfiguration->getParam(SwiftMailerKeys::SMTP_USERNAME));
            }

            // load the SMTP password from the configuration
            if ($transportConfiguration->hasParam(SwiftMailerKeys::SMTP_PASSWORD)) {
                $transport->setPassword($transportConfiguration->getParam(SwiftMailerKeys::SMTP_PASSWORD));
            }
        }

        // load the mailer factory
        $mailerFactory = $swiftMailerConfiguration->getMailerFactory();

        // initialize, create and return the swift mailer instance
        return $mailerFactory::newInstance($transport);
    }
}
