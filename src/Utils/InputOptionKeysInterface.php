<?php

/**
 * TechDivision\Import\Utils\InputOptionKeysInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Interface for classes containing the available input option keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface InputOptionKeysInterface extends \ArrayAccess
{

    /**
     * Input key for the --serial option.
     *
     * @var string
     */
    const SERIAL = 'serial';

    /**
     * The input option key for the system name to use.
     *
     * @var string
     */
    const SYSTEM_NAME = 'system-name';

    /**
     * The input option key for the path to the configuration file to use.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * The input option key for the Magento installation directory.
     *
     * @var string
     */
    const INSTALLATION_DIR = 'installation-dir';

    /**
     * The input option key for the Magento installation directory.
     *
     * @var string
     */
    const CONFIGURATION_DIR = 'config-dir';

    /**
     * The input option key for the directory containing the files to be imported.
     *
     * @var string
     */
    const SOURCE_DIR = 'source-dir';

    /**
     * The input option key for the directory containing the imported files.
     *
     * @var string
     */
    const TARGET_DIR = 'target-dir';

    /**
     * The input option key for the directory containing the archived imported files.
     *
     * @var string
     */
    const ARCHIVE_DIR = 'archive-dir';

    /**
     * The input option key for the directory containing the flag to archive the imported files.
     *
     * @var string
     */
    const ARCHIVE_ARTEFACTS = 'archive-artefacts';

    /**
     * The input option key for the directory containing the flag to artefacts have to be cleared.
     *
     * @var string
     */
    const CLEAR_ARTEFACTS = 'clear-artefacts';

    /**
     * The input option key for the Magento edition, EE or CE.
     *
     * @var string
     */
    const MAGENTO_EDITION = 'magento-edition';

    /**
     * The input option key for the Magento version, e. g. 2.1.0.
     *
     * @var string
     */
    const MAGENTO_VERSION = 'magento-version';

    /**
     * The input option key for the database ID to use.
     *
     * @var string
     */
    const USE_DB_ID = 'use-db-id';

    /**
     * The input option key for the PDO DSN to use.
     *
     * @var string
     */
    const DB_PDO_DSN = 'db-pdo-dsn';

    /**
     * The input option key for the DB username to use.
     *
     * @var string
     */
    const DB_USERNAME = 'db-username';

    /**
     * The input option key for the DB password to use.
     *
     * @var string
     */
    const DB_PASSWORD = 'db-password';

    /**
     * The input option key for the DB table prefix to use.
     *
     * @var string
     */
    const DB_TABLE_PREFIX = 'db-table-prefix';

    /**
     * The input option key for the debug mode.
     *
     * @var string
     */
    const DEBUG_MODE = 'debug-mode';

    /**
     * The input option key for the log level to use.
     *
     * @var string
     */
    const LOG_LEVEL = 'log-level';

    /**
     * The input option key for the PID filename to use.
     *
     * @var string
     */
    const PID_FILENAME = 'pid-filename';

    /**
     * The input option key for the destination pathname to use.
     *
     * @var string
     */
    const DEST = 'dest';

    /**
     * The input option key for the single transaction flag.
     *
     * @var string
     */
    const SINGLE_TRANSACTION = 'single-transaction';

    /**
     * The input option key for additional params that has to be merged into the application configuration.
     *
     * @var string
     */
    const PARAMS = 'params';

    /**
     * The input option key for the path to additional params as file that has to be merged into the application configuration.
     *
     * @var string
     */
    const PARAMS_FILE = 'params-file';

    /**
     * The input option key for the flag to enable the cache functionality or not.
     *
     * @var string
     */
    const CACHE_ENABLED = 'cache-enabled';

    /**
     * The input option key for the move files prefix.
     *
     * @var string
     */
    const MOVE_FILES_PREFIX = 'move-files-prefix';

    /**
     * The input option key for the custom configuration directory.
     *
     * @var string
     */
    const CUSTOM_CONFIGURATION_DIR = 'custom-configuration-dir';

    /**
     * The input option key for the number of validation issues that has to be rendered on the console.
     *
     * @var string
     */
    const RENDER_VALIDATION_ISSUES = 'render-validation-issues';

    /**
     * The input option key for the number of debug serials rendered on the console.
     *
     * @var string
     */
    const RENDER_DEBUG_SERIALS = 'render-debug-serials';

    /**
     * The input option key for the empty value in attribute option import
     *
     * @var string
     */
    const EMPTY_ATTRIBUTE_VALUE_CONSTANT = 'empty-attribute-value-constant';

    /**
     * The input option key for the strict mode.
     *
     * @var string
     */
    const STRICT_MODE = 'strict-mode';

    /**
     * The input option key for the Step log infos.
     *
     * @var string
     */
    const LOG_FILE = 'log-file';

    /**
     * The input option key for the strict mode.
     *
     * @var string
     */
    const CONFIG_OUTPUT = 'config-output';

    /**
     * Query whether or not the passed input option is valid.
     *
     * @param string $inputOption The input option to query for
     *
     * @return boolean TRUE if the input option is valid, else FALSE
     */
    public function isInputOption($inputOption);
}
