<?php

/**
 * TechDivision\Import\UtilsBackendTypeKeys
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
 * Utility class containing the available backend types.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class BackendTypeKeys
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
     * Key for the backend type 'static'.
     *
     * @var string
     */
    const BACKEND_TYPE_STATIC = 'static';

    /**
     * Key for the backend type 'datetime'.
     *
     * @var string
     */
    const BACKEND_TYPE_DATETIME = 'datetime';

    /**
     * Key for the backend type 'decimal'.
     *
     * @var string
     */
    const BACKEND_TYPE_DECIMAL = 'decimal';

    /**
     * Key for the backend type 'float'.
     *
     * @var string
     */
    const BACKEND_TYPE_FLOAT = 'float';

    /**
     * Key for the backend type 'int'.
     *
     * @var string
     */
    const BACKEND_TYPE_INT = 'int';

    /**
     * Key for the backend type 'text'.
     *
     * @var string
     */
    const BACKEND_TYPE_TEXT = 'text';

    /**
     * Key for the backend type 'varchar'.
     *
     * @var string
     */
    const BACKEND_TYPE_VARCHAR = 'varchar';
}
