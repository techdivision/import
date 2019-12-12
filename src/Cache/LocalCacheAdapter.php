<?php

/**
 * TechDivision\Import\Cache\GenericCacheAdapter
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

use TechDivision\Import\Utils\CacheKeyUtilInterface;

/**
 * Local cache adapter implementation.
 *
 * This cache adapter guarantees maximum performance but can eventually not be used
 * in a distributed environemnt where you want to use e. g. Redis for caching.
 *
 * If you are in a distributed environment, have a look at the GenericCacheAdapter
 * that can wrap any PSR-6 compatible cache implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @see       \TechDivision\Import\Cache\GenericCacheAdapter
 */
class LocalCacheAdapter implements CacheAdapterInterface
{

    /**
     * Trait that provides custom cache adapter functionality.
     *
     * @var TechDivision\Import\Cache\CacheAdapterTrait
     */
    use CacheAdapterTrait;

    /**
     * The array with the tags.
     *
     * @var array
     */
    protected $tags = array();

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * References that links to another cache entry.
     *
     * @var array
     */
    protected $references = array();

    /**
     * The cache key utility instance.
     *
     * @var \TechDivision\Import\Utils\CacheKeyUtilInterface
     */
    protected $cacheKeyUtil;

    /**
     * Initialize the cache handler with the passed cache and configuration instances.
     *
     * @param \TechDivision\Import\Utils\CacheKeyUtilInterface $cacheKeyUtil The cache key utility instance
     */
    public function __construct(CacheKeyUtilInterface $cacheKeyUtil)
    {
        $this->cacheKeyUtil = $cacheKeyUtil;
    }

    /**
     * Creates a unique cache key from the passed data.
     *
     * @param mixed   $data      The date to create the cache key from
     * @param boolean $usePrefix Flag to signal using the prefix or not
     *
     * @return string The generated cache key
     */
    public function cacheKey($data, $usePrefix = true)
    {
        return $this->cacheKeyUtil->cacheKey($data, $usePrefix);
    }

    /**
     * Query whether or not a cache value for the passed cache key is available.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the a value is available, else FALSE
     */
    public function isCached($key)
    {
        return isset($this->cache[$this->resolveReference($this->cacheKey($key))]);
    }

    /**
     * Inversion of the isCached() method.
     *
     * @param string $key The cache key to query for
     *
     * @return boolean TRUE if the value is not available, else FALSE
     */
    public function notCached($key)
    {
        return !$this->isCached($key);
    }

    /**
     * Add's a cache reference from one key to another.
     *
     * @param string $from The key to reference from
     * @param string $to   The key to reference to
     *
     * @return void
     */
    public function addReference($from, $to)
    {
        $this->references[$this->cacheKey($from)] = $this->cacheKey($to);
    }

    /**
     * Resolve's the cache key.
     *
     * @param string $from The cache key to resolve
     *
     * @return string The resolved reference
     */
    protected function resolveReference($from)
    {

        // query whether or not a reference exists
        if (isset($this->references[$from])) {
            return $this->references[$from];
        }

        // return the passed reference
        return $from;
    }

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
    public function toCache($key, $value, array $references = array(), array $tags = array(), $override = true, $time = null)
    {

        // query whether or not the key has already been used
        if (isset($this->cache[$this->resolveReference($uniqueKey = $this->cacheKey($key))]) && $override === false) {
            throw new \Exception(
                sprintf(
                    'Try to override data with key "%s"',
                    $uniqueKey
                )
            );
        }

        // set the attribute in the registry
        $this->cache[$uniqueKey] = $value;

        // prepend the tags with the cache key
        array_walk($tags, function (&$tag) {
            $tag = $this->cacheKey($tag);
        });

        // tag the unique key
        foreach ($tags as $tag) {
            $this->tags[$tag][] = $uniqueKey;
        }

        // also register the references if given
        foreach ($references as $from => $to) {
            $this->addReference($from, $to);
        }
    }

    /**
     * Returns a new cache item for the passed key
     *
     * @param string $key The cache key to return the item for
     *
     * @return mixed The value for the passed key
     */
    public function fromCache($key)
    {
        if (isset($this->cache[$uniqueKey = $this->resolveReference($this->cacheKey($key))])) {
            return $this->cache[$uniqueKey];
        }
    }

    /**
     * Flush the cache and remove the references.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->tags = array();
        $this->cache = array();
        $this->references = array();
    }

    /**
     * Invalidate the cache entries for the passed tags.
     *
     * @param array $tags The tags to invalidate the cache for
     *
     * @return void
     */
    public function invalidateTags(array $tags)
    {

        // prepend the tags with the cache key
        array_walk($tags, function (&$tag) {
            $tag = $this->cacheKey($tag);
        });

        // remove all the references of items that has one of the passed tags
        foreach ($tags as $tag) {
            if (isset($this->tags[$tag])) {
                foreach ($this->tags[$tag] as $to) {
                    // clean-up the references that reference to the key
                    if ($from = array_search($to, $this->references)) {
                        unset($this->references[$from]);
                    }
                    // clean-up the cache entry itself
                    if (isset($this->cache[$to])) {
                        unset($this->cache[$to]);
                    }
                }
            }
        }
    }

    /**
     * Remove the item with the passed key and all its references from the cache.
     *
     * @param string $key The key of the cache item to Remove
     *
     * @return void
     */
    public function removeCache($key)
    {

        // delete the item with the passed key
        unset($this->cache[$this->resolveReference($uniqueKey = $this->cacheKey($key))]);

        // query whether or not the references exists and has to be removed
        if (isset($this->references[$uniqueKey])) {
            unset($this->references[$uniqueKey]);
        }
    }
}
