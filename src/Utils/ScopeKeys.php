<?php

/**
 * TechDivision\Import\Utils\ScopeKeys
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
 * Utility class containing the keys Magento uses to persist values in the "core_config_data table".
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ScopeKeys
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
     * Name for the 'default' scope.
     *
     * @var string
     */
    const SCOPE_DEFAULT = 'default';

    /**
     * Name for the 'stores' scope.
     *
     * @var string
     */
    const SCOPE_WEBSITES = 'websites';

    /**
     * Name for the 'stores' scope.
     *
     * @var string
     */
    const SCOPE_STORES = 'stores';
}
