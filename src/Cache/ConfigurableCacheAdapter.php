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

use Psr\Cache\CacheItemPoolInterface;
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
class ConfigurableCacheAdapter extends GenericCacheAdapter
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the cache handler with the passed cache and configuration instances.
     * .
     * @param \Psr\Cache\CacheItemPoolInterface           $cache         The cache instance
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(CacheItemPoolInterface $cache, ConfigurationInterface $configuration)
    {

        // pass the cache adapter to the parent constructor
        parent::__construct($cache);

        // set the configuration instance
        $this->configuration = $configuration;
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
            return parent::isCached($key);
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
     * @param boolean $override   Flag that allows to override an exising cache entry
     *
     * @return void
     */
    public function toCache($key, $value, array $references = array(), $override = false)
    {

        // query whether or not the cache is enabled
        if ($this->configuration->isCacheEnabled()) {
            parent::toCache($key, $value, $references, $override);
        }
    }
}
