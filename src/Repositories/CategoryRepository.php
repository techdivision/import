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
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

/**
 * Repository implementation to load category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
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
     * The statement to load the categories.
     *
     * @var \PDOStatement
     */
    protected $categoriesStmt;

    /**
     * The statement to load the root categories.
     *
     * @var \PDOStatement
     */
    protected $rootCategoriesStmt;

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
        $this->categoriesStmt = $this->getConnection()->prepare($this->myStmt = $utilityClassName::CATEGORIES);
        $this->rootCategoriesStmt = $this->getConnection()->prepare($this->myStmt = $utilityClassName::ROOT_CATEGORIES);
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

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function findAllRootCategories()
    {

        // query whether or not we've already loaded the value
        if (!isset($this->cache[__METHOD__])) {
            // try to load the categories
            $this->rootCategoriesStmt->execute();

            // initialize the array with the store code as key
            $rootCategories = array();
            foreach ($this->rootCategoriesStmt->fetchAll() as $category) {
                $rootCategories[$category['code']] = $category;
            }

            // append the root categories to the cache
            $this->cache[__METHOD__] = $rootCategories;
        }

        // return the categories from the cache
        return $this->cache[__METHOD__];
    }
}
