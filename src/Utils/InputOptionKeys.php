<?php

/**
 * TechDivision\Import\Utils\InputOptionKeys
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
 * Utility class containing the available input option keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class InputOptionKeys
{

    /**
     * Input key for the --configuration option.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * Input key for the --magento-edition option.
     *
     * @var string
     */
    const MAGENTO_EDITION = 'magento-edition';

    /**
     * Input key for the --magento-version option.
     *
     * @var string
     */
    const MAGENTO_VERSION = 'magento-version';

    /**
     * Input key for the --source-date-format option.
     *
     * @var string
     */
    const SOURCE_DATE_FORMAT = 'source-date-format';

    /**
     * Input key for the --db-pdo-dsn option.
     *
     * @var string
     */
    const DB_PDO_DSN = 'db-pdo-dsn';

    /**
     * Input key for the --db-username option.
     *
     * @var string
     */
    const DB_USERNAME = 'db-username';

    /**
     * Input key for the --db-password option.
     *
     * @var string
     */
    const DB_PASSWORD = 'db-password';

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
}
