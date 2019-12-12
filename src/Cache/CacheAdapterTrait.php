<?php

/**
 * TechDivision\Import\Cache\CacheAdapterTrait
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
 * Trait that provides custom cache adapter functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @see       \TechDivision\Import\Cache\GenericCacheAdapter
 */
trait CacheAdapterTrait
{

    /**
     * Query whether or not a cache value for the passed cache key is available.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the a value is available, else FALSE
     */
    abstract public function isCached($key);

    /**
     * Inversion of the isCached() method.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the value is not available, else FALSE
     */
    abstract public function notCached($key);

    /**
     * Add the passed item to the cache.
     *
     * @param string  $key        The cache key to use
     * @param mixed   $value      The value that has to be cached
     * @param array   $references An array with references to add
     * @param array   $tags       An array with tags to add
     * @param boolean $override   Flag that allows to override an exising cache entry
     * @param integer $time       The TTL in seconds for the passed item
     *
     * @return void
     */
    abstract public function toCache($key, $value, array $references = array(), array $tags = array(), $override = true, $time = null);

    /**
     * Returns a new cache item for the passed key
     *
     * @param string $key The cache key to return the item for
     *
     * @return mixed The value for the passed key
     */
    abstract public function fromCache($key);

    /**
     * Raises the value for the attribute with the passed key by one.
     *
     * @param mixed $key         The key of the attribute to raise the value for
     * @param mixed $counterName The name of the counter to raise
     *
     * @return integer The counter's new value
     */
    public function raiseCounter($key, $counterName)
    {

        // initialize the counter
        $counter = 0;

        // raise/initialize the value
        if ($this->isCached($key)) {
            // try to load the array with the counters from the cache
            $value = $this->fromCache($key);
            // query whether or not a counter is available and try to load it
            if (is_array($value) && isset($value[$counterName])) {
                $counter = $value[$counterName];
            }
        }

        // set the counter value back to the cache item/cache
        $this->mergeAttributesRecursive($key, array($counterName => ++$counter));

        // return the new value
        return $counter;
    }

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
    public function mergeAttributesRecursive($key, array $attributes)
    {

        // if the key not exists, simply add the new attributes
        if ($this->notCached($key)) {
            $this->toCache($key, $attributes);
            return;
        }

        // if the key exists and the value is an array, merge it with the passed array
        if (is_array($value = $this->fromCache($key))) {
            $this->toCache($key, array_replace_recursive($value, $attributes), array(), array(), true);
            return;
        }

        // throw an exception if the key exists, but the found value is not of type array
        throw new \Exception(
            sprintf(
                'Can\'t merge attributes, because value for key "%s" already exists, but is not of type array',
                $key
            )
        );
    }
}
