<?php

/**
 * TechDivision\Import\Cache\NamespacedArrayCachePoolFactory
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

use Cache\Namespaced\NamespacedCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
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
class NamespacedArrayCachePoolFactory
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the cache adapter factory with the passed configuration instances.
     * .
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Creates and returns the cache pool instance.
     *
     * @return \Cache\Namespaced\NamespacedCachePool The namespaced cache pool instance
     */
    public function createCachePool()
    {
        return new NamespacedCachePool(new ArrayCachePool(), $this->configuration->getSerial());
    }
}
