<?php

/**
 * TechDivision\Import\Repositories\CachedRepositoryInterface
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
 * The interface for a cached repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CachedRepositoryInterface extends RepositoryInterface
{

    /**
     * Prepares a unique cache key for the passed query name and params.
     *
     * @param string $queryName The query name to prepare the cache key for
     * @param array  $params    The query params
     *
     * @return string The prepared cache key
     */
    public function cacheKey($queryName, array $params);

    /**
     * Query whether or not a cache value for the passed cache key is available.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the a value is available, else FALSE
     */
    public function isCached($cacheKey);

    /**
     * Inversion of the isCached() method.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the value is not available, else FALSE
     */
    public function notCached($cacheKey);

    /**
     * Add the passed value to the cache.
     *
     * @param string $cacheKey The cache key
     * @param mixed  $value    The value to cache
     *
     * @return void
     */
    public function toCache($cacheKey, $value);

    /**
     * Query whether or not a value for the passed cache key exists or not. If yes, the value
     * will be returned, else an exception will be thrown.
     *
     * @param string $cacheKey The cache key to return the value for
     *
     * @return mixed The value for the passed cache key
     * @throws \Exception Is thrown, if no value is available
     */
    public function fromCache($cacheKey);
}
