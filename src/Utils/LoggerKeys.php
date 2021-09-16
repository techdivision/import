<?php

/**
 * TechDivision\Import\Utils\LoggerKeys
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
 * A utility class for the logger keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LoggerKeys
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
     * The key for system logger.
     *
     * @var string
     */
    const SYSTEM = 'system';

    /**
     * The key for mail logger.
     *
     * @var string
     */
    const MAIL = 'mail';

    /**
     * The key for param 'bubble'.
     *
     * @var string
     */
    const BUBBLE = 'bubble';

    /**
     * The key for param 'log-level'.
     *
     * @var string
     */
    const LOG_LEVEL = 'log-level';
}
