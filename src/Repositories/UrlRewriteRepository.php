<?php

/**
 * TechDivision\Import\Repositories\UrlRewriteRepository
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Repository implementation to load URL rewrite data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteRepository extends AbstractFinderRepository implements UrlRewriteRepositoryInterface, FinderAwareEntityRepositoryInterface
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID));
    }

    /**
     * Return's an array with the URL rewrites for the passed entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityId($entityType, $entityId)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE => $entityType,
            MemberNames::ENTITY_ID   => $entityId
        );

        // load and return the URL rewrites
        foreach ($this->getFinder(SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID)->find($params) as $result) {
            yield $result;
        }
    }

    /**
     * Return's an array with the URL rewrites for the passed entity type, entity and store ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE => $entityType,
            MemberNames::ENTITY_ID   => $entityId,
            MemberNames::STORE_ID    => $storeId
        );

        // load and return the URL rewrites
        foreach ($this->getFinder(SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID)->find($params) as $result) {
            yield $result;
        }
    }


    /**
     * Return's an array with all URL rewrites
     *
     * @return array|null The country region data
     */
    public function findAll()
    {
        foreach ($this->getFinder(SqlStatementKeys::URL_REWRITES)->find() as $result) {
            yield $result;
        }
    }

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::URL_REWRITE_ID;
    }

    /**
     * Return's the finder's entity name.
     *
     * @return string The finder's entity name
     */
    public function getEntityName()
    {
        return CacheKeys::URL_REWRITE;
    }

    /**
     * Return's the entity unique key name.
     *
     * @return string The name of the entity's unique key
     */
    public function getUniqueKeyName()
    {
        return $this->getPrimaryKeyName();
    }
}
