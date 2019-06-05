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

use TechDivision\Import\ConfigurationInterface;

/**
 * Configurable cache adapter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
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
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the cache handler with the passed cache and configuration instances.
     * .
     * @param \TechDivision\Import\Cache\CacheAdapterInterface $cacheAdapter  The cache instance
     * @param \TechDivision\Import\ConfigurationInterface      $configuration The configuration instance
     */
    public function __construct(CacheAdapterInterface $cacheAdapter, ConfigurationInterface $configuration)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->configuration = $configuration;
    }

    /**
     * Add the passed tag as default tag.
     *
     * @param string $tag The default tag to add
     *
     * @return void
     */
    public function addTag($tag)
    {
        $this->cacheAdapter->addTag($tag);;
    }

    /**
     * The default tags to use, e. g. the serial of the actual import.
     *
     * @return array The array with the default tags
     */
    public function getTags()
    {
        return $this->cacheAdapter->getTags();
    }

    /**
     * Prepares a unique cache key for the passed query name and params.
     *
     * @param string $uniqueName A unique name used to prepare the cache key with
     * @param array  $params     The query params
     *
     * @return string The prepared cache key
     */
    public function cacheKey($uniqueName, array $params)
    {
        return $this->cacheAdapter->cacheKey($uniqueName, $params);
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
     * Invalidat the items for the passed tags.
     *
     * @param array $tags The tags to invalidate the items for
     *
     * @return void
     */
    public function invalidateTags(array $tags = array())
    {
        $this->cacheAdapter->invalidateTags($tags);
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
        $this->cacheAdapter->removeCache($key);
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
        if ($this->configuration->isCacheEnabled()) {
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
     * @param array   $tags       An array with additional tags to use
     * @param boolean $override   Flag that allows to override an exising cache entry
     *
     * @return void
     */
    public function toCache($key, $value, array $references = array(), array $tags = array(), $override = false)
    {

        // query whether or not the cache is enabled
        if ($this->configuration->isCacheEnabled()) {
            $this->cacheAdapter->toCache($key, $value, $references, $tags, $override);
        }
    }
}
