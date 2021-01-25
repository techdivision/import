<?php

/**
 * TechDivision\Import\Utils\CategoryPathUtil
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CategoryPathUtilInterface
{

    public function normalize(string $path) : string;

    /**
     * Create a CSV compatible string from the passed category path.
     *
     * @param string The normalized category path (usually from the DB)
     *
     * @return string The denormalized category path for the import file
     * @see \TechDivision\Import\Utils\CategoryPathUtilInterface::denormalize()
     */
    public function denormalize(string $path) : string;


    public function explode(string $path) : array;

    public function implode(array $elements) : string;
}
