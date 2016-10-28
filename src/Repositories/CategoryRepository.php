<?php

/**
 * TechDivision\Import\Repositories\CategoryRepository
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
class CategoryRepository extends AbstractRepository
{

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Return's an array of the categories with the passed values.
     *
     * @param array The names of the categories to return
     *
     * @return array The array with all available stores
     */
    public function findAllByValues($values)
    {

        // prepare the cache key
        $vals = implode(',', $values);

        // query whether or not we've already loaded the values
        if (!isset($this->cache[$vals])) {
            // load the SQL statement for the category query
            $utilityClassName = $this->getUtilityClassName();
            $sql = str_replace('?', $vals, $utilityClassName::CATEGORIES);

            // load the categories with the passed values and return them
            if ($stmt = $this->getConnection()->query($sql)) {
                $this->cache[$vals] = $stmt->fetchAll();
            } else {
                error_log("Can't find categories for: $sql");
            }
        }

        // return the values from the cache
        return $this->cache[$vals];
    }
}
