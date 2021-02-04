<?php

/**
 * TechDivision\Import\Cache\ConfigurableCacheAdapter
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

use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Utils\CacheTypes;

/**
 * Configurable cache adapter implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Cache\Collection\ConfigurableCacheAdapter
 */
class ConfigurableCacheAdapter implements CacheAdapterInterface
{

    /**
     * The cache adatper instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * The TTL used to cache items.
     *
     * @var integer
     */
    protected $time = null;

    /**
     * The flag if the cache is anabled or not.
     *
     * @var boolean
     */
    protected $enabled = true;

    /**
     * The array with the default tags.
     *
     * @var string
     */
    protected $tags = array();

    /**
     * Initialize the cache handler with the passed cache and configuration instances.
     * .
     * @param \TechDivision\Import\Cache\CacheAdapterInterface          $cacheAdapter  The cache instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     * @param string                                                    $type          The cache type to use
     */
    public function __construct(
        CacheAdapterInterface $cacheAdapter,
        ConfigurationInterface $configuration,
        $type = CacheTypes::TYPE_STATIC
    ) {

        // set the cache adapter to use
        $this->cacheAdapter = $cacheAdapter;

        // append the serial to the array with the default tags
        $this->tags = array_merge(array($configuration->getSerial()));

        // load the configuration for the passed cache type
        if ($cacheConfiguration = $configuration->getCacheByType($type)) {
            // initialize the ttl and the enabled flag
            $this->time = $cacheConfiguration->getTime();
            $this->enabled = $cacheConfiguration->isEnabled();
        } else {
            $this->enabled = ($configuration->isCacheEnabled() || $type === CacheTypes::TYPE_STATIC);
        }
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
        return $this->cacheAdapter->cacheKey($data, $usePrefix);
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
        return $this->cacheAdapter->notCached($key);
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
        $this->cacheAdapter->addReference($from, $to);
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
        return $this->cacheAdapter->fromCache($key);
    }

    /**
     * Flush the cache and remove the references.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->cacheAdapter->flushCache();
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
        $this->cacheAdapter->invalidateTags($tags);
    }

    /**
     * Remove the item with the passed key and all its references from the cache.
     *
     * @param string $key               The key of the cache item to Remove
     * @param bool   $cleanUpReferences TRUE if the references has to be cleaned-up, else FALSE (default)
     *
     * @return void
     */
    public function removeCache($key, $cleanUpReferences = false)
    {
        $this->cacheAdapter->removeCache($key, $cleanUpReferences);
    }

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
        return $this->cacheAdapter->raiseCounter($key, $counterName);
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
        $this->cacheAdapter->mergeAttributesRecursive($key, $attributes);
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

        // query whether or not the item has been cached, and if yes if the cache is valid
        if ($this->enabled) {
            return $this->cacheAdapter->isCached($key);
        }

        // return FALSE in all other cases
        return false;
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

        // query whether or not the cache is enabled
        if ($this->enabled) {
            $this->cacheAdapter->toCache($key, $value, $references, array_merge($this->tags, $tags), $override, $time ? $time : $this->time);
        }
    }
}
