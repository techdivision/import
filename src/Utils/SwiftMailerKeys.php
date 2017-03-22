<?php

/**
 * TechDivision\Import\Utils\SwiftMailerKeys
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

namespace TechDivision\Import\Utils;

/**
 * A utility class for the swift mailer configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SwiftMailerKeys
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * The key for param 'to'.
     *
     * @var string
     */
    const TO = 'to';

    /**
     * The key for param 'from'.
     *
     * @var string
     */
    const FROM = 'from';

    /**
     * The key for param 'subject'.
     *
     * @var string
     */
    const SUBJECT = 'subject';

    /**
     * The key for param 'content-type'.
     *
     * @var string
     */
    const CONTENT_TYPE = 'content-type';

    /**
     * The key for param 'smtp-host'.
     *
     * @var string
     */
    const SMTP_HOST = 'smtp-host';

    /**
     * The key for param 'smtp-port'.
     *
     * @var string
     */
    const SMTP_PORT = 'smtp-port';

    /**
     * The key for param 'smtp-security'.
     *
     * @var string
     */
    const SMTP_SECURITY = 'smtp-security';

    /**
     * The key for param 'smtp-username'.
     *
     * @var string
     */
    const SMTP_USERNAME = 'smtp-username';

    /**
     * The key for param 'smtp-password'.
     *
     * @var string
     */
    const SMTP_PASSWORD = 'smtp-password';

    /**
     * The key for param 'smtp-auth-mode'.
     *
     * @var string
     */
    const SMTP_AUTH_MODE = 'smtp-auth-mode';
}
