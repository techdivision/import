<?php

/**
 * TechDivision\Import\Repositories\EavAttributeOptionValueRepository
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

namespace TechDivision\Import\Repositories;

/**
 * Repository implementation to load EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractCachedRepository extends AbstractRepository implements CachedRepositoryInterface
{

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
     * Prepares a unique cache key for the passed query name and params.
     *
     * @param string $queryName The query name to prepare the cache key for
     * @param array  $params    The query params
     *
     * @return string The prepared cache key
     */
    public function cacheKey($queryName, array $params)
    {
        return sprintf('%s-%s', $queryName, implode('-', $params));
    }

    /**
     * Query whether or not a cache value for the passed cache key is available.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the a value is available, else FALSE
     */
    public function isCached($cacheKey)
    {
        return isset($this->cache[$this->resolveReference($cacheKey)]);
    }

    /**
     * Inversion of the isCached() method.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the value is not available, else FALSE
     */
    public function notCached($cacheKey)
    {
        return !$this->isCached($cacheKey);
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
        $this->references[$from] = $to;
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
     * Add the passed value to the cache.
     *
     * @param string $cacheKey   The cache key
     * @param mixed  $value      The value to cache
     * @param array  $references An array with references to add
     *
     * @return void
     */
    public function toCache($cacheKey, $value, array $references = array())
    {

        // add the value to the cache
        $this->cache[$cacheKey] = $value;

        // also register the references if given
        foreach ($references as $from => $to) {
            $this->references[$from] = $to;
        }
    }

    /**
     * Query whether or not a value for the passed cache key exists. If yes, the value
     * will be returned, else an exception will be thrown.
     *
     * @param string $cacheKey The cache key to return the value for
     *
     * @return mixed The value for the passed cache key
     * @throws \Exception Is thrown, if no value is available
     */
    public function fromCache($cacheKey)
    {

        // query whether or not a value for the cache key is available
        if (isset($this->cache[$resolvedCacheKey = $this->resolveReference($cacheKey)])) {
            return $this->cache[$resolvedCacheKey];
        }

        // throw an exception if not
        throw new \Exception(sprintf('Can\'t find cached value for key "%s"', $cacheKey));
    }

    /**
     * Flush the cache, or the value with the passed key.
     *
     * @param mixed|null $cacheKey The key of the value to flush
     *
     * @return void
     */
    public function flushCache($cacheKey = null)
    {

        // flush the complete cache, if NO cache key has been passed
        if ($cacheKey === null) {
            $this->references = $this->cache = array();
            return;
        }

        // only flush the value with the passed cache key
        if (isset($this->cache[$resolvedCacheKey = $this->resolveReference($cacheKey)])) {
            unset($this->cache[$resolvedCacheKey]);
        }
    }
}
