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
     * Name for the column 'root-directory'.
     *
     * @var string
     */
    const ROOT = 'root';

    /**
     * Name for the column 'level'.
     *
     * @var string
     */
    const LEVEL = 'level';

    /**
     * Name for the column 'name'.
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * Name for the column 'handlers'.
     *
     * @var string
     */
    const HANDLERS = 'handlers';

    /**
     * Name for the column 'processors'.
     *
     * @var string
     */
    const PROCESSORS = 'processors';
}
