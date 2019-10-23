<?php

/**
 * TechDivision\Import\Utils\ConfigurationKeys
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
 * Utility class containing the configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationKeys
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
     * Name for the configuration key 'root'.
     *
     * @var string
     */
    const ROOT = 'root';

    /**
     * Name for the configuration key 'level'.
     *
     * @var string
     */
    const LEVEL = 'level';

    /**
     * Name for the configuration key 'name'.
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * Name for the configuration key 'handlers'.
     *
     * @var string
     */
    const HANDLERS = 'handlers';

    /**
     * Name for the configuration key 'processors'.
     *
     * @var string
     */
    const PROCESSORS = 'processors';

    /**
     * Name for the configuration key 'cache-warmers'.
     *
     * @var string
     */
    const CACHE_WARMERS = 'cache-warmers';

    /**
     * Name for the configuration key 'clean-up-url-rewrites'.
     *
     * @var string
     */
    const CLEAN_UP_URL_REWRITES = 'clean-up-url-rewrites';

    /**
     * Name for the configuration key 'clean-up-empty-columns',
     *
     * @var string
     */
    const CLEAN_UP_EMPTY_COLUMNS = 'clean-up-empty-columns';

    /**
     * Name for the configuration key 'cache-enabled',
     *
     * @var string
     */
    const CACHE_ENABLED = 'cache-enabled';

    /**
     * Name for the configuration key 'custom-validations'.
     *
     * @var string
     */
    const CUSTOM_VALIDATIONS = 'custom-validations';
}
