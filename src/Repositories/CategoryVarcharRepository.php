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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Repositories;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
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
        $sql = str_replace('?', $vals, $utilityClassName::CATEGORY_VARCHARS_BY_ENTITY_IDS);

        // load the categories with the passed values and return them
        if ($stmt = $this->getConnection()->query($sql)) {
            return $stmt->fetchAll();
        }
    }
}
