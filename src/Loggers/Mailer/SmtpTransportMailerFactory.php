<?php

/**
 * TechDivision\Import\Loggers\Mailer\SmtpTransportMailerFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers\Mailer;

use Exception;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use TechDivision\Import\Utils\MailerKeys;
use TechDivision\Import\Configuration\Mailer\TransportConfigurationInterface;

/**
 * Factory implementation for a mailer with SMTP transport (Symfony Mailer).
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
     * Creates a new mailer instance based on the passed transport configuration.
     *
     * @param TransportConfigurationInterface $transportConfiguration The mailer configuration
     *
     * @return Mailer|MailerInterface The mailer instance
     * @throws Exception
     */
    public function factory(TransportConfigurationInterface $transportConfiguration)
    {
        // load the SMTP host from the configuration
        if ($transportConfiguration->hasParam(MailerKeys::SMTP_HOST)) {
            $smtpHost = $transportConfiguration->getParam(MailerKeys::SMTP_HOST);
        } else {
            $smtpHost = 'localhost';
        }

        // load the SMTP port from the configuration
        if ($transportConfiguration->hasParam(MailerKeys::SMTP_PORT)) {
            $smtpPort = $transportConfiguration->getParam(MailerKeys::SMTP_PORT);
        } else {
            $smtpPort = null;
        }

        // load the SMTP security from the configuration
        if ($transportConfiguration->hasParam(MailerKeys::SMTP_SECURITY)) {
            $smtpSecurity = $transportConfiguration->getParam(MailerKeys::SMTP_SECURITY);
        } else {
            $smtpSecurity = null;
        }

        // load optional authentication settings
        if ($transportConfiguration->hasParam(MailerKeys::SMTP_USERNAME)) {
            $username = $transportConfiguration->getParam(MailerKeys::SMTP_USERNAME);
        } else {
            $username = null;
        }

        if ($transportConfiguration->hasParam(MailerKeys::SMTP_PASSWORD)) {
            $password = $transportConfiguration->getParam(MailerKeys::SMTP_PASSWORD);
        } else {
            $password = null;
        }

        if ($transportConfiguration->hasParam(MailerKeys::SMTP_AUTH_MODE)) {
            $authMode = $transportConfiguration->getParam(MailerKeys::SMTP_AUTH_MODE);
        } else {
            $authMode = null;
        }

        // build DSN
        $auth = '';
        if (!empty($username)) {
            $auth = sprintf('%s:%s@', rawurlencode($username), rawurlencode((string)$password));
        }
        $hostPort = sprintf('%s%s', $smtpHost, $smtpPort ? ":$smtpPort" : '');
        $query = [];
        if (!empty($smtpSecurity)) {
            $query[] = sprintf('encryption=%s', $smtpSecurity);
        }
        if (!empty($authMode)) {
            $query[] = sprintf('auth_mode=%s', $authMode);
        }
        $dsn = sprintf('smtp://%s%s%s%s', $auth, $hostPort, $query ? '?' : '', implode('&', $query));

        // create Symfony Mailer from DSN
        $transport = Transport::fromDsn($dsn);

        return new Mailer($transport);
    }
}
