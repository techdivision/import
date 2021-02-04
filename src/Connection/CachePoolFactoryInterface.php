<?php

/**
 * TechDivision\Import\Connection\CachePoolFactoryInterface
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

/**
 * The interface for all cache pool factory implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Cache\CachePoolFactoryInterface
 */
interface CachePoolFactoryInterface
{

    /**
     * Create's and return's the cache pool to use.
     *
     * @return \Psr\Cache\CacheItemPoolInterface The initialized connection
     */
    public function createCachePool();
}
