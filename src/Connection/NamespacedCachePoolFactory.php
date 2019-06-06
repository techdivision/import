<?php

/**
 * TechDivision\Import\Connection\NamespacedArrayCachePoolFactory
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

namespace TechDivision\Import\Connection;

use Cache\Namespaced\NamespacedCachePool;
use TechDivision\Import\ConfigurationInterface;

/**
 * Factory for namespaced array cache pool instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class NamespacedCachePoolFactory implements CachePoolFactoryInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The cache factory instance.
     *
     * @var \TechDivision\Import\Connection\CachePoolFactoryInterface
     */
    protected $cachePoolFactory;

    /**
     * Initialize the cache adapter factory with the passed configuration instances.
     * .
     * @param \TechDivision\Import\ConfigurationInterface               $configuration    The configuration instance
     * @param \TechDivision\Import\Connection\CachePoolFactoryInterface $cachePoolFactory The cache factory instance
     */
    public function __construct(ConfigurationInterface $configuration, CachePoolFactoryInterface $cachePoolFactory)
    {
        $this->configuration = $configuration;
        $this->cachePoolFactory = $cachePoolFactory;
    }

    /**
     * Creates and returns the cache pool instance.
     *
     * @return \Cache\Namespaced\NamespacedCachePool The namespaced cache pool instance
     */
    public function createCachePool()
    {
        return new NamespacedCachePool($this->cachePoolFactory->createCachePool(), $this->configuration->getSerial());
    }
}
