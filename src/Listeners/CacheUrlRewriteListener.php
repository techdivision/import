<?php

/**
 * TechDivision\Import\Listeners\CacheUrlRewriteListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Dbal\Actions\CachedActionInterface;

/**
 * A listener implementation that updates the cache after a row has been updated.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheUrlRewriteListener extends AbstractListener
{

    /**
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * Initializes the listener with the cache adapter and the system loggers.
     *
     * @param \TechDivision\Import\Cache\CacheAdapterInterface $cacheAdapter The cache adapter instance
     */
    public function __construct(CacheAdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
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
     * Return's the finder's unique key.
     *
     * @return string The unique key
     */
    public function getKey()
    {
        return SqlStatementKeys::URL_REWRITE_BY_REQUEST_PATH_AND_STORE_ID;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface                            $event  The event that triggered the listener
     * @param \TechDivision\Import\Dbal\Actions\CachedActionInterface $action The action instance that triggered the event
     * @param array                                                   $row    The row to be cached
     *
     * @return void
     */
    public function handle(EventInterface $event, CachedActionInterface $action = null, array $row = array())
    {

        // prepare the unique cache key for the EAV attribute option value
        $uniqueKey = array($this->getEntityName() => $row[$this->getPrimaryKeyName()]);

        // initialize the params
        $params = array(
            MemberNames::REQUEST_PATH => $row[MemberNames::REQUEST_PATH],
            MemberNames::STORE_ID     => $row[MemberNames::STORE_ID]
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array($this->getKey() => $params), false);

        // query whether or not which status the passed entity has
        switch ($row[EntityStatus::MEMBER_NAME]) {
            case EntityStatus::STATUS_CREATE:
                // in case we've a new entity, add it to the cache adapter
                $this->cacheAdapter->toCache($uniqueKey, $row, array($cacheKey => $uniqueKey));
                break;
            case EntityStatus::STATUS_UPDATE:
                // in case we've an existing one, update it
                $this->cacheAdapter->toCache($uniqueKey, $row, array($cacheKey => $uniqueKey), array(), true);
                break;
            default:
                // in all other cases, remove the existing entity from the cache to allow reloading it
                if ($this->cacheAdapter->isCached($uniqueKey)) {
                    $this->cacheAdapter->removeCache($uniqueKey);
                }
        }
    }
}
