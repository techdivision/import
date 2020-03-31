<?php

/**
 * TechDivision\Import\Subjects\FileWriter\Filters\DefaultOkFileFilter
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

namespace TechDivision\Import\Subjects\FileWriter\Filters;

/**
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DefaultOkFileFilter implements FileWriterFilterInterface
{

    public function getFlags()
    {
        return ARRAY_FILTER_USE_BOTH;
    }

    /**
     *
     * @param array  $v
     * @param string $k
     *
     * @return bool TRUE if the value with the actual key should be in the array, else FALSE
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