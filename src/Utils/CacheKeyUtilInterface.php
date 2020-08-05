<?php

/**
 * TechDivision\Import\Utils\CacheKeys
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
 * The interface for all utility class implementations to create cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CacheKeyUtilInterface
{

    /**
     * Creates a unique cache key from the passed data.
     *
     * @param mixed   $data      The date to create the cache key from
     * @param boolean $usePrefix Flag to signal using the prefix or not
     *
     * @return string The generated cache key
     * @throws \Exception Is thrown if the passed data is not supported to create a cache key from
     */
    public function cacheKey($data, $usePrefix = true);
}
