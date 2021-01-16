<?php

/**
 * TechDivision\Import\Repositories\UrlRewriteRepositoryInterface
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
 * Interface for a URL rewrite data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface UrlRewriteRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with the available URL rewrites.
     *
     * @return array The available URL rewrites
     */
    public function findAll();

    /**
     * Return's an array with the available URL rewrites
     *
     * @return array The array with the rewrites, grouped by request path and store ID
     */
    public function findAllGroupedByRequestPathAndStoreId();

    /**
     * Return's an array with the URL rewrites for the passed entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * Return's an array with the URL rewrites for the passed entity type, entity and store ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);

    /**
     * Load's and return's the URL rewrite for the given request path and store ID.
     *
     * ATTENTION: This method access the registry to make sure the parallel processes will access
     * the same URL rewrites. The initial data the will be added the registry will be loaded with
     * the method `UrlRewriteRepository::findAllGroupedByRequestPathAndStoreId()`
     *
     * @param string $requestPath The request path to load the URL rewrite for
     * @param int    $storeId     The store ID to load the URL rewrite for
     *
     * @return array|null The URL rewrite found for the given request path and store ID
     * @see \TechDivision\Import\Repositories\UrlRewriteRepository::findAllGroupedByRequestPathAndStoreId()
     */
    public function findOneByRequestPathAndStoreId(string $requestPath, int $storeId);
}
