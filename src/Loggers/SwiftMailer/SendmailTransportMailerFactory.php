<?php

/**
 * TechDivision\Import\Loggers\SwiftMailer\SendmailTransportMailerFactory
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

namespace TechDivision\Import\Loggers\SwiftMailer;

use TechDivision\Import\Utils\SwiftMailerKeys;
use TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface;

/**
 * Factory implementation for a swift mailer with a simple sendmail transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SendmailTransportMailerFactory implements TransportMailerFactoryInterface
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

        // initialize and load the sendmail command parameter
        $command = '/usr/sbin/sendmail -bs';
        if ($transportConfiguration->hasParam(SwiftMailerKeys::COMMAND)) {
            $command = $transportConfiguration->getParam(SwiftMailerKeys::COMMAND);
        }

        // initialize and create the mailer transport instance
        $transport = \Swift_SendmailTransport::newInstance($command);

        // initialize, create and return the swift mailer instance
        return \Swift_Mailer::newInstance($transport);
    }
}
