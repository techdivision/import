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
     * @param array $entityIds The array with the category IDs
     *
     * @return mixed The category varchar values
     */
    public function findAllByEntityIds(array $entityIds)
    {

        // prepare the cache key
        $vals = implode(',', $entityIds);
        $sql = str_replace('?', $vals, $this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS_BY_ENTITY_IDS));

        // load the categories with the passed values and return them
        if ($stmt = $this->getConnection()->query($sql)) {
            return $stmt->fetchAll();
        }
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

        // prepare the cache key
        $sql = str_replace('?', $entityId, $this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS_BY_ENTITY_IDS));

        // load the categories with the passed values and return them
        if ($stmt = $this->getConnection()->query($sql)) {
            return $stmt->fetch();
        }
        return [];
    }
}
