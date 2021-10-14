<?php

/**
 * TechDivision\Import\Utils\ProductTypes
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
 * Utility class containing the product types.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductTypes
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
     * Name for the column 'simple'.
     *
     * @var string
     */
    const SIMPLE = 'simple';

    /**
     * Name for the column 'bundle'.
     *
     * @var string
     */
    const BUNDLE = 'bundle';

    /**
     * Name for the column 'configurable'.
     *
     * @var string
     */
    const CONFIGURABLE = 'configurable';

    /**
     * Name for the column 'configurable'.
     *
     * @var string
     */
    const VIRTUAL = 'virtual';

    /**
     * Name for the column 'downloadable'.
     *
     * @var string
     */
    const DOWNLOADABLE = 'downloadable';
}
