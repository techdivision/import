<?php

/**
 * TechDivision\Import\Loggers\SwiftMailer\TransportMailerFactoryInterface
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

namespace TechDivision\Import\Loggers\SwiftMailer;

use TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface;

/**
 * Interface for mailer transport factory implementations, e. g. a simple Swift sendmail transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 201 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface TransportMailerFactoryInterface
{

    /**
     * Creates a new swift mailer instance based on the passed transport configuration.
     *
     * @param \TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface $transportConfiguration The mailer configuration
     *
     * @return \Swift_Mailer The mailer instance
     */
    public function factory(TransportConfigurationInterface $transportConfiguration);
}
