<?php

/**
 * TechDivision\Import\Services\RegistryProcessor
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

namespace TechDivision\Import\Services;

use TechDivision\Import\Cache\CacheAdapterInterface;

/**
 * Array based implementation of a registry.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RegistryProcessor implements RegistryProcessorInterface
{

    /**
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * Initialize the repository with the passed connection and utility class name.
     *
     * @param \TechDivision\Import\Cache\CacheAdapterInterface $cacheAdapter The cache adapter instance
     */
    public function __construct(CacheAdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Register the passed attribute under the specified key in the registry.
     *
     * @param string  $key        The cache key to use
     * @param mixed   $value      The value that has to be cached
     * @param array   $references An array with references to add
     * @param array   $tags       An array with additional tags to use
     * @param boolean $override   Flag that allows to override an exising cache entry
     *
     * @return void
     * @throws \Exception Is thrown, if the key has already been used
     */
    public function setAttribute($key, $value, array $references = array(), array $tags = array(), $override = false)
    {
        $this->cacheAdapter->toCache($key, $value, $references, $tags, $override);
    }

    /**
     * Return's the attribute with the passed key from the registry.
     *
     * @param mixed $key The key to return the attribute for
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
        return $this->cacheAdapter->fromCache($key);
    }

    /**
     * Query whether or not an attribute with the passed key has already been registered.
     *
     * @param mixed $key The key to query for
     *
     * @return boolean TRUE if the attribute has already been registered, else FALSE
     */
    public function hasAttribute($key)
    {
        return $this->cacheAdapter->isCached($key);
    }

    /**
     * Remove the attribute with the passed key from the registry.
     *
     * @param mixed $key The key of the attribute to return
     *
     * @return void
     */
    public function removeAttribute($key)
    {
        $this->cacheAdapter->removeAttribute($key);
    }

    /**
     * Flush the cache.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->cacheAdapter->flushCache();
    }

    /**
     * Invalidate the items with the passed tags.
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
        return $this->cacheAdapter->mergeAttributesRecursive($key, $attributes);
    }
}
