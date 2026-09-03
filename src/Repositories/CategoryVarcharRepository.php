<?php

/**
 * TechDivision\Import\Repositories\CategoryVarcharRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load category varchar data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryVarcharRepository extends AbstractRepository implements CategoryVarcharRepositoryInterface
{
    /**
     * The default number of entity IDs to load in a single batch query.
     *
     * @var integer
     */
    private const int BATCH_SIZE = 1000;

    /**
     * Cache for the category varchar values loaded by entity ID.
     *
     * @var array
     */
    private array $cacheByEntityId = [];

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity IDs.
     *
     * @param array   $entityIds The array with the category IDs
     * @param integer $batchSize The maximum number of entity IDs per query
     *
     * @return array The category varchar values
     */
    public function findAllByEntityIds(array $entityIds, int $batchSize = self::BATCH_SIZE): array
    {
        $result = [];
        $entityIds = array_values(array_unique(array_map('intval', $entityIds)));

        if ($entityIds === []) {
            return $result;
        }

        $batchSize = (int)$batchSize;
        if ($batchSize < 1) {
            $batchSize = self::BATCH_SIZE;
        }

        foreach (array_chunk($entityIds, $batchSize) as $chunk) {
            $vals = implode(',', $chunk);
            $sql = str_replace('?', $vals, $this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS_BY_ENTITY_IDS));

            if ($stmt = $this->getConnection()->query($sql)) {
                foreach ($stmt->fetchAll() as $row) {
                    $result[] = $row;
                }
            }
        }

        // return the collected result rows
        return $result;
    }

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity ID.
     *
     * @param int $entityId The category ID
     *
     * @return array The category varchar values
     */
    public function findByEntityId($entityId)
    {
        $entityId = (int)$entityId;

        if (array_key_exists($entityId, $this->cacheByEntityId)) {
            return $this->cacheByEntityId[$entityId];
        }

        $sql = str_replace(
            '?',
            (string)$entityId,
            $this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS_BY_ENTITY_IDS)
        );

        if ($stmt = $this->getConnection()->query($sql)) {
            return $this->cacheByEntityId[$entityId] = $stmt->fetch();
        }

        return $this->cacheByEntityId[$entityId] = [];
    }
}
