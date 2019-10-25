<?php

/**
 * TechDivision\Import\Loaders\BooleanOptionValueLoader
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Loader for boolean option values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class BooleanOptionValueLoader implements LoaderInterface
{

    /**
     * Array with the string => boolean mapping.
     *
     * @var array
     */
    protected $booleanValues = array(
        'true'  => 1,
        'yes'   => 1,
        '1'     => 1,
        'false' => 0,
        'no'    => 0,
        '0'     => 0
    );

    /**
     * Loads and returns data the custom validation data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->booleanValues;
    }
}
