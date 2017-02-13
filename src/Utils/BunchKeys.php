<?php

/**
 * TechDivision\Import\Utils\BunchKeys
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
 * A utility class for the bunch handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class BunchKeys
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
     * The key for the prefix part found in the filename.
     *
     * @var string
     */
    const PREFIX = 'prefix';

    /**
     * The key for the filename part found in the filename.
     *
     * @var string
     */
    const FILENAME = 'filename';

    /**
     * The key for the counter part found in the filename.
     *
     * @var string
     */
    const COUNTER = 'counter';

    /**
     * Return's an array with all available bunch keys.
     *
     * @return array The available bunch keys
     */
    public static function getAllKeys()
    {
        return array(BunchKeys::PREFIX, BunchKeys::FILENAME, BunchKeys::COUNTER);
    }
}
