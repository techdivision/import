<?php

/**
 * TechDivision\Import\Loggers\Mailer\TransportMailerFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers\Mailer;

use TechDivision\Import\Configuration\Mailer\TransportConfigurationInterface;

/**
 * Interface for mailer transport factory implementations, e. g. a simple sendmail transport.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 201 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface TransportMailerFactoryInterface
{

    /**
     * Creates a new mailer instance based on the passed transport configuration.
     *
     * @param TransportConfigurationInterface $transportConfiguration The mailer configuration
     *
     * @return mixed The mailer instance
     */
    public function factory(TransportConfigurationInterface $transportConfiguration);
}
