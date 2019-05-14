<?php

/**
 *TechDivision\Import\Cache\CacheItem
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

/**
 * A simple, PSR-6 conform cache item implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheItem implements CacheItemInterface
{

    /**
     * The default timeout.
     *
     * @var integer
     */
    const DEFAULT_TIMEOUT = 1400;

    /**
     * The cache key.
     *
     * @var string
     */
    protected $key;

    /**
     * The cached value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * The UNIX timestamp with the expiration date.
     *
     * @var integer
     */
    protected $expiry = 0;

    /**
     * Initializes the cache item with the key.
     *
     * @param mixed $key The cache key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string  The key string for this cache item.
     * @see \Psr\Cache\CacheItemInterface::getKey()
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value The serializable value to be stored.
     *
     * @return static The invoked object.
     * @see \Psr\Cache\CacheItemInterface::set()
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return boolean TRUE if the request resulted in a cache hit. FALSE otherwise.
     * @see \Psr\Cache\CacheItemInterface::isHit()
     */
    public function isHit()
    {
        return ($this->expiry === 0 && $this->value !== null) || $this->expiry > time();
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed The value corresponding to this cache item's key, or null if not found.
     * @see \Psr\Cache\CacheItemInterface::get()
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration The point in time after which the item MUST be considered expired. If null is passed explicitly, a default value MAY be used. If none is set, the value should be stored permanently or for as long as the implementation allows.
     *
     * @return static The called object.
     * @see \Psr\Cache\CacheItemInterface::expiresAt()
     * @throws \InvalidArgumentException
     */
    public function expiresAt($expiration)
    {

        if (null === $expiration) {
            $this->expiry = CacheItem::DEFAULT_TIMEOUT > 0 ? time() + CacheItem::DEFAULT_TIMEOUT : null;
        } elseif ($expiration instanceof \DateTimeInterface) {
            $this->expiry = (int) $expiration->format('U');
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expiration date must implement DateTimeInterface or be null, "%s" given',
                    is_object($expiration) ? get_class($expiration) : gettype($expiration)
                )
            );
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time The period of time from the present after which the item MUST be considered expired. An integer parameter is understood to be the time in seconds until expiration. If null is passed explicitly, a default value MAY be used. If none is set, the value should be stored permanently or for as long as the implementation allows.
     *
     * @return static The called object.
     * @see \Psr\Cache\CacheItemInterface::expiresAfter()
     * @throws \InvalidArgumentException
     */
    public function expiresAfter($time)
    {

        if (null === $time) {
            $this->expiry = CacheItem::DEFAULT_TIMEOUT > 0 ? time() + CacheItem::DEFAULT_TIMEOUT : null;
        } elseif ($time instanceof \DateInterval) {
            $this->expiry = (int) \DateTime::createFromFormat('U', time())->add($time)->format('U');
        } elseif (\is_int($time)) {
            $this->expiry = $time + time();
        } else {
            throw new \InvalidArgumentException(sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given', \is_object($time) ? \get_class($time) : \gettype($time)));
        }

        return $this;
    }
}
