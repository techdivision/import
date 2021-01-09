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

use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractFinderRepository;

/**
 * Repository implementation to load URL rewrite data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteRepository extends AbstractFinderRepository implements UrlRewriteRepositoryInterface
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The prepared statement to load the existing URL rewrites.
     *
     * @var \PDOStatement
     */
    protected $urlRewritesStmt;

    /**
     * The prepared statement to load the existing URL rewrites by their entity type and ID.
     *
     * @var \PDOStatement
     */
    protected $urlRewritesByEntityTypeAndEntityIdStmt;

    /**
     * The prepared statement to load the existing URL rewrites by their entity type, entity and store ID.
     *
     * @var \PDOStatement
     */
    protected $urlRewritesByEntityTypeAndEntityIdAndStoreIdStmt;

    /**
     * The prefix to load the URL rewrites with the given request path and store ID from the registry.
     *
     * @var string
     */
    protected $urlRewritesByRequestPathAndStoreIdPrefix;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::STORES));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITE_BY_REQUEST_PATH_AND_STORE_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID));
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
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::URL_REWRITE_ID;
    }

    /**
     * Return's an array with the available URL rewrites.
     *
     * @return array The available URL rewrites
     */
    public function findAll()
    {
        foreach ($this->getFinder(SqlStatementKeys::URL_REWRITES)->find() as $result) {
            yield $result;
        }
    }

    /**
     * Return's an array with the available URL rewrites
     *
     * @return array The array with the rewrites, grouped by request path and store ID
     * @todo Refactor to a yielded version also
     */
    public function findAllGroupedByRequestPathAndStoreId()
    {

        // initialize the array with the available URL rewrites
        $urlRewrites = array();

        // iterate over all available URL rewrites
        foreach ($this->findAll() as $urlRewrite) {
            // append the URL rewrite for the given request path and store ID combination
            $urlRewrites[$urlRewrite[MemberNames::REQUEST_PATH]][$urlRewrite[MemberNames::STORE_ID]] = $urlRewrite;
        }

        // return the array with the URL rewrites
        return $urlRewrites;
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
     * Load's and return's the URL rewrite for the given request path and store ID.
     *
     * @param string $requestPath The request path to load the URL rewrite for
     * @param int    $storeId     The store ID to load the URL rewrite for
     *
     * @return array|null The URL rewrite found for the given request path and store ID
     */
    public function findOneByRequestPathAndStoreId(string $requestPath, int $storeId)
    {

        // initialize the params
        $params = array(
            MemberNames::REQUEST_PATH => $requestPath,
            MemberNames::STORE_ID     => $storeId
        );

        // load and return the URL rewrite with the passed request path and store ID
        return $this->getFinder(SqlStatementKeys::URL_REWRITE_BY_REQUEST_PATH_AND_STORE_ID)->find($params);
    }
}
