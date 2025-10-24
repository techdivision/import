<?php

/**
 * TechDivision\Import\Utils\MailerKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * A utility class for the mailer configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MailerKeys
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
    public const string TO = 'to';

    /**
     * The key for param 'from'.
     *
     * @var string
     */
    public const string FROM = 'from';

    /**
     * The key for param 'subject'.
     *
     * @var string
     */
    public const string SUBJECT = 'subject';

    /**
     * The key for param 'content-type'.
     *
     * @var string
     */
    public const string CONTENT_TYPE = 'content-type';

    /**
     * The key for param 'smtp-host'.
     *
     * @var string
     */
    public const string SMTP_HOST = 'smtp-host';

    /**
     * The key for param 'smtp-port'.
     *
     * @var string
     */
    public const string SMTP_PORT = 'smtp-port';

    /**
     * The key for param 'smtp-security'.
     *
     * @var string
     */
    public const string SMTP_SECURITY = 'smtp-security';

    /**
     * The key for param 'smtp-username'.
     *
     * @var string
     */
    public const string SMTP_USERNAME = 'smtp-username';

    /**
     * The key for param 'smtp-password'.
     *
     * @var string
     */
    public const string SMTP_PASSWORD = 'smtp-password';

    /**
     * The key for param 'smtp-auth-mode'.
     *
     * @var string
     */
    public const string SMTP_AUTH_MODE = 'smtp-auth-mode';

    /**
     * The key for param 'command'.
     *
     * @var string
     */
    public const string COMMAND = 'command';
}
