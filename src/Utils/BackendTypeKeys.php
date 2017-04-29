<?php

/**
 * TechDivision\Import\UtilsBackendTypeKeys
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
 * Utility class containing the available backend types.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
