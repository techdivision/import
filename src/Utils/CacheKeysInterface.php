<?php

/**
 * TechDivision\Import\Utils\CacheKeysInterface
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
 * Interface for cache key implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Cache\Utils\CacheKeysInterface
 */
interface CacheKeysInterface extends \ArrayAccess
{

    /**
     * Query whether or not the passed cache key is valid.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the cache key is valid, else FALSE
     */
    public function isCacheKey($cacheKey);

    /**
     * Returns the cache key of the actual instance.
     *
     * @return string The cache key
     */
    public function getCacheKey();
}
