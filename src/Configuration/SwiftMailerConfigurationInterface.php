<?php

/**
 * TechDivision\Import\Configuration\SwiftMailerConfigurationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * The swift mailer configuration interface.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface SwiftMailerConfigurationInterface extends ParamsConfigurationInterface
{

    /**
     * Return's the factory used to create the swift mailer instance.
     *
     * @return string The factory
     */
    public function getFactory();

    /**
     * Return's the swift mailer factory that creates the mailer instance.
     *
     * @return string The swift mailer factory
     */
    public function getMailerFactory();

    /**
     * Return's the swift mailer transport configuration to use.
     *
     * @return \TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface The transport configuration to use
     */
    public function getTransport();
}
