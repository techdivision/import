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

use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Utils\CoreConfigDataKeys;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Repositories\Finders\FinderFactoryInterface;

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
     * The core config data loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $coreConfigDataLoader;


    /**
     * The array with the entity type > configuration key mapping.
     *
     * @var array
     */
    protected $entityTypeConfigKeyMapping = array(
        'product'  => CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX,
        'category' => CoreConfigDataKeys::CATALOG_SEO_CATEGORY_URL_SUFFIX
    );

    /**
     * The array with the entity type and store view specific suffixes.
     *
     * @var array
     */
    protected $suffixes = array();

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     * @param \TechDivision\Import\Repositories\Finders\FinderFactoryInterface  $finderFactory          The finder factory instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface          $registryProcessor      The registry processor instance
     * @param \TechDivision\Import\Loaders\LoaderInterface                      $coreConfigDataLoader   The core config data loader instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        FinderFactoryInterface $finderFactory,
        RegistryProcessorInterface $registryProcessor,
        LoaderInterface $coreConfigDataLoader
    ) {

        // set the registry processor and the core config data loader instances
        $this->registryProcessor = $registryProcessor;
        $this->coreConfigDataLoader = $coreConfigDataLoader;

        // pass the connection, SQL statement repository and the finder factory to the parent class
        parent::__construct($connection, $sqlStatementRepository, $finderFactory);
    }

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
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID));

        // initialize the prefix to load the URL rewrites with the
        // given request path and store ID from the registry
        $this->urlRewritesByRequestPathAndStoreIdPrefix = sprintf(
            '%s.%s.%s',
            RegistryKeys::STATUS,
            RegistryKeys::GLOBAL_DATA,
            RegistryKeys::URL_REWRITES
        );

        // initialize the URL suffixs from the Magento core configuration
        foreach ($this->getFinder(SqlStatementKeys::STORES)->find() as $store) {
            // prepare the array with the entity type and store ID specific suffixes
            foreach ($this->entityTypeConfigKeyMapping as $entityType => $configKey) {
                // load the suffix for the given entity type => configuration key and store ID
                $suffix = $this->coreConfigDataLoader->load($configKey, '.html', ScopeKeys::SCOPE_DEFAULT, $storeId = $store[MemberNames::STORE_ID]);
                // register the suffux in the array
                $this->suffixes[$entityType][$storeId] = $suffix;
            }
        }
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
     */
    public function findAllGroupedByRequestPathAndStoreId()
    {

        // initialize the array with the available URL rewrites
        $urlRewrites = array();

        // iterate over all available URL rewrites
        foreach ($this->findAll() as $urlRewrite) {
            // load the request path as we need it as key for the array
            $requestPath = $urlRewrite[MemberNames::REQUEST_PATH];

            // query whether or not a suffix has been registered for the given entity type
            // and store ID. If yes, we strip the suffix to have a unified access via the
            // stripped reqeust path  and the store ID
            if (isset($this->suffixes[$urlRewrite[MemberNames::ENTITY_TYPE]][$urlRewrite[MemberNames::STORE_ID]])) {
                // load the suffix from the available ones
                $suffix = $this->suffixes[$urlRewrite[MemberNames::ENTITY_TYPE]][$urlRewrite[MemberNames::STORE_ID]];
                // remove the suffix from the request path to unify access
                $requestPath = str_replace($suffix, null, $requestPath);
            }

            // initialize the array with the URL
            // rewrites for each request path
            if (isset($urlRewrites[$requestPath]) === false) {
                $urlRewrites[$requestPath] = array();
            }

            // append the URL rewrite for the given request path and store ID combination
            $urlRewrites[$requestPath][$urlRewrite[MemberNames::STORE_ID]] = $urlRewrite;
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
    public function findOneByUrlRewriteByRequestPathAndStoreId(string $requestPath, int $storeId)
    {
        try {
            return $this->registryProcessor->load(sprintf('%s.%s.%d', $this->urlRewritesByRequestPathAndStoreIdPrefix, $requestPath, $storeId));
        } catch (\InvalidArgumentException $iae) {
            // do nothing here, because we simply
            // can't find the appropriate rewrite
        }
    }
}
