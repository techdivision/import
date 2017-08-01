<?php

/**
 * TechDivision\Import\Utils\DependencyInjectionKeys
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
 * @link      https://github.com/techdivision/import-app-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * A utility class for the DI service keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-app-simple
 * @link      http://www.techdivision.com
 */
class DependencyInjectionKeys
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
     * The key for the application instance.
     *
     * @var string
     */
    const APPLICATION = 'application';

    /**
     * The key for the configuration service.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * The key for the CSV import adapter service.
     *
     * @var string
     */
    const IMPORT_ADAPTER_IMPORT_CSV = 'import.adapter.import.csv';

    /**
     * The key for the CSV export adapter service.
     *
     * @var string
     */
    const IMPORT_ADAPTER_EXPORT_CSV = 'import.adapter.export.csv';

    /**
     * The key for the simple PHP filesystem adapter service.
     *
     * @var string
     */
    const IMPORT_ADAPTER_FILESYSTEM_FACTORY_PHP = 'import.adapter.filesystem.factory.php';

    /**
     * The key for the simple PHP filesystem adapter service.
     *
     * @var string
     */
    const IMPORT_ADAPTER_FILESYSTEM_FACTORY_LEAGUE = 'import.adapter.filesystem.factory.league';
}
