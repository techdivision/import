<?php

/**
 * TechDivision\Import\Loggers\Mailer\SendmailTransportMailerFactory
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
use Symfony\Component\Mailer\Transport\SendmailTransport;
use TechDivision\Import\Utils\MailerKeys;
use TechDivision\Import\Configuration\Mailer\TransportConfigurationInterface;

/**
 * Factory implementation for a mailer with a simple sendmail transport (Symfony Mailer).
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SendmailTransportMailerFactory implements TransportMailerFactoryInterface
{
    /**
     * Creates a new mailer instance based on the passed transport configuration.
     *
     * @param TransportConfigurationInterface $transportConfiguration The mailer configuration
     *
     * @return MailerInterface The mailer instance
     * @throws Exception
     */
    public function factory(TransportConfigurationInterface $transportConfiguration)
    {
        // initialize and load the sendmail command parameter
        $command = '/usr/sbin/sendmail -bs';
        if ($transportConfiguration->hasParam(MailerKeys::COMMAND)) {
            $command = $transportConfiguration->getParam(MailerKeys::COMMAND);
        }

        // initialize and create the mailer transport instance
        $transport = new SendmailTransport($command);

        // initialize, create and return the Symfony mailer instance
        return new Mailer($transport);
    }
}
