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

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->categoriesStmt = $this->getConnection()->prepare($utilityClassName::CATEGORIES);
    }

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function findAll()
    {

        // query whether or not we've already loaded the value
        if (!isset($this->cache[__METHOD__])) {
            // try to load the categories
            $this->categoriesStmt->execute();
            $this->cache[__METHOD__] = $this->categoriesStmt->fetchAll();
        }

        // return the categories from the cache
        return $this->cache[__METHOD__];
    }
}
