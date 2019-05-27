<?php

/**
 * TechDivision\Import\Cache\CacheAdapterInterface
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

namespace TechDivision\Import\Cache;

/**
 * Interface for cache adapter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CacheAdapterInterface
{

    /**
     * Prepares a unique cache key for the passed query name and params.
     *
     * @param string $uniqueName A unique name used to prepare the cache key with
     * @param array  $params     The query params
     *
     * @return string The prepared cache key
     */
    public function cacheKey($uniqueName, array $params);

    /**
     * Query whether or not a cache value for the passed cache key is available.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the a value is available, else FALSE
     */
    public function isCached($key);

    /**
     * Inversion of the isCached() method.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the value is not available, else FALSE
     */
    public function notCached($key);

    /**
     * Add's a cache reference from one key to another.
     *
     * @param string $from The key to reference from
     * @param string $to   The key to reference to
     *
     * @return void
     */
    public function addReference($from, $to);

    /**
     * Add the passed item to the cache.
     *
     * @param string  $key        The cache key to use
     * @param mixed   $value      The value that has to be cached
     * @param array   $references An array with references to add
     * @param boolean $override   Flag that allows to override an exising cache entry
     *
     * @return void
     */
    public function toCache($key, $value, array $references = array(), $override = false);

    /**
     * Returns a new cache item for the passed key
     *
     * @param string $key The cache key to return the item for
     *
     * @return mixed The value for the passed key
     */
    public function fromCache($key);

    /**
     * Flush the cache, or the value with the passed key.
     *
     * @param mixed|null $key The key of the value to flush
     *
     * @return void
     */
    public function flushCache($key = null);

    /**
     * Raises the value for the attribute with the passed key by one.
     *
     * @param mixed $key         The key of the attribute to raise the value for
     * @param mixed $counterName The name of the counter to raise
     *
     * @return integer The counter's new value
     */
    public function raiseCounter($key, $counterName);

    /**
     * This method merges the passed attributes with an array that
     * has already been added under the passed key.
     *
     * If no value will be found under the passed key, the attributes
     * will simply be registered.
     *
     * @param mixed $key        The key of the attributes that has to be merged with the passed ones
     * @param array $attributes The attributes that has to be merged with the exising ones
     *
     * @return void
     * @throws \Exception Is thrown, if the already registered value is no array
     * @link http://php.net/array_replace_recursive
     */
    public function mergeAttributesRecursive($key, array $attributes);
}
