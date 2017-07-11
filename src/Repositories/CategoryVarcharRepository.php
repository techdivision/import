<?php

/**
 * TechDivision\Import\Repositories\CategoryVarcharRepository
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
 * Repository implementation to load category varchar data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryVarcharRepository extends AbstractRepository
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

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // prepare the cache key
        $vals = implode(',', $entityIds);
        $sql = str_replace('?', $vals, $this->getUtilityClass()->find($utilityClassName::CATEGORY_VARCHARS_BY_ENTITY_IDS));

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
        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // prepare the cache key
        $sql = str_replace('?', $entityId, $this->getUtilityClass()->find($utilityClassName::CATEGORY_VARCHARS_BY_ENTITY_IDS));

        // load the categories with the passed values and return them
        if ($stmt = $this->getConnection()->query($sql)) {
            return $stmt->fetch();
        }
    }
}
