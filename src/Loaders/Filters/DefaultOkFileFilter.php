<?php

/**
 * TechDivision\Import\Loaders\Filters\DefaultOkFileFilter
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders\Filters;

/**
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DefaultOkFileFilter implements FilterInterface
{

    /**
     * The filter's unique name.
     *
     * @return string The unique name
     */
    public function getName() : string
    {
        return (string) DefaultOkFileFilter::class;
    }

    /**
     * Return's the flag used to define what will be passed to the callback invoked
     * by the `array_filter()` method.
     *
     * @return int The flag
     */
    public function getFlag() : int
    {
        return ARRAY_FILTER_USE_BOTH;
    }

    /**
     * This is the callback method that will be called by the invoking `array_filter` function.
     *
     * @param mixed $v The value that has to be filtered
     *
     * @return bool TRUE if the value should be in the array, else FALSE
     */
    public function __invoke($v, $k) : bool
    {

        foreach ($v as $f) {

            $matches = array();

            $pattern = '/^(?<prefix>.*)_(?<filename>.*)_.*\.csv$/';

            if (preg_match($pattern, $f, $matches)) {
                if (preg_match(sprintf('/^.*\/%s_%s\.ok$/', $matches['prefix'], $matches['filename']), $k)) {
                    return true;
                }
            }
        }

        return false;
    }
}