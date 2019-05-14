<?php

/**
 *TechDivision\Import\Cache\ArrayCache
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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * A PSR-6 compatible cache implementation.
 *
 * ```php
 * // create a new item by trying to get it from the cache
 * $accessToken = $arrayCache->getItem('access_token');
 *
 * // assign a value to the item and save it
 * $accessToken->set('YWraeE4Esro5IsncUEM2amwyh4dS5IzL');
 * $arrayCache->save($accessToken);
 *
 * // retrieve the cache item
 * $cacheItem = $arrayCache->getItem('access_token');
 * if (!cacheItem->isHit()) {
 *     // ... item does not exists in the cache
 * }
 * // retrieve the value stored by the item
 * $accessToken = cacheItem->get();
 *
 * // remove the cache item
 * $arrayCache->deleteItem('access_token');
 * ```
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ArrayCache extends \ArrayObject implements CacheItemPoolInterface
{

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key The key for which to check existence.
     *
     * @return boolean True if item exists in the cache, false otherwise.
     * @see \Psr\Cache\CacheItemPoolInterface::hasItem()
     */
    public function hasItem($key)
    {
        return isset($this[$key]);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return boolean TRUE if the pool was successfully cleared. FALSE if there was an error.
     * @see \Psr\Cache\CacheItemPoolInterface::clear()
     */
    public function clear()
    {

        // load the iterator
        $iterator = $this->getIterator();

        // iterate over the cached items and delete them
        while ($iterator->valid()) {
            $this->deleteItem($iterator->current()->getKey());
            $iterator->next();
        }

        // return TRUE if we're successful
        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return boolean TRUE if the item was successfully persisted. FALSE if there was an error.
     * @see \Psr\Cache\CacheItemPoolInterface::save()
     */
    public function save(CacheItemInterface $item)
    {
        $this[$item->getKey()] = $item;
        return true;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     *
     * @return \Psr\Cache\CacheItemInterface The corresponding Cache Item.
     * @see \Psr\Cache\CacheItemPoolInterface::getItem()
     */
    public function getItem($key)
    {

        // query whether or not the item exists
        if ($this->hasItem($key)) {
            return $this[$key];
        }

        // create a new cache item and return it
        return new CacheItem($key);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys An indexed array of keys of items to retrieve.
     *
     * @return array|\Traversable A traversable collection of Cache Items keyed by the cache keys of each item. A Cache item will be returned for each key, even if that key is not found. However, if no keys are specified then an empty traversable MUST be returned instead.
     * @see \Psr\Cache\CacheItemPoolInterface::getItems()
     */
    public function getItems(array $keys = [])
    {

        // initialize the array for the cache items
        $items = array();

        // iterate over the passed keys and load the items
        foreach ($keys as $key) {
            $items[] = $this->getItem($key);
        }

        // return the items
        return $items;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key The key to delete.
     *
     * @return boolean TRUE if the item was successfully removed. FALSE if there was an error.
     *
     * @see \Psr\Cache\CacheItemPoolInterface::deleteItem()
     */
    public function deleteItem($key)
    {
        unset($this[$key]);
        return true;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys An array of keys that should be removed from the pool.
     *
     * @return boolean TRUE if the items were successfully removed. False if there was an error.
     * @see \Psr\Cache\CacheItemPoolInterface::deleteItems()
     */
    public function deleteItems(array $keys)
    {

        // iterate over the keys and delete the items
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }

        // return TRUE if we've been successful
        return true;
    }

    /**
     * Returns new ArrayIterator.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(get_object_vars($this));
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param \Psr\Cache\CacheItemInterface $item The cache item to save.
     *
     * @return boolean FALSE if the item could not be queued or if a commit was attempted and failed. True otherwise.
     * @see \Psr\Cache\CacheItemPoolInterface::saveDeferred()
     * @throws \Exception
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        throw new \Exception(sprintf('Method "%s" is not yet implemented', __METHOD__));
    }

    /**
     * Persists any deferred cache items.
     *
     * @return boolean TRUE if all not-yet-saved items were successfully saved or there were none. FALSE otherwise.
     * @see \Psr\Cache\CacheItemPoolInterface::commit()
     * @throws \Exception
     */
    public function commit()
    {
        throw new \Exception(sprintf('Method "%s" is not yet implemented', __METHOD__));
    }
}
