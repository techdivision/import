<?php

/**
 * TechDivision\Import\Loggers\SwiftMailer\SmtpTransportMailerFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers\SwiftMailer;

use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface;

/**
 * Factory implementation for a swift mailer with SMTP transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SmtpTransportMailerFactory implements TransportMailerFactoryInterface
{

    /**
     * Creates a new swift mailer instance based on the passed transport configuration.
     *
     * @param \TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface $transportConfiguration The mailer configuration
     *
     * @return \Swift_Mailer The mailer instance
     */
    public function factory(TransportConfigurationInterface $transportConfiguration)
    {

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
        $transport = new \Swift_SmtpTransport($smtpHost, $smtpPort, $smtpSecurity);

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

        // initialize, create and return the swift mailer instance
        return new \Swift_Mailer($transport);
    }
}
