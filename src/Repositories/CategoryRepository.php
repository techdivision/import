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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Repository implementation to load category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{

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

        // initialize the prepared statements
        $this->categoriesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORIES));
        $this->rootCategoriesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::ROOT_CATEGORIES));
        $this->categoriesByStoreViewStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORIES_BY_STORE_VIEW));
    }

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function findAll()
    {
        // try to load the categories
        $this->categoriesStmt->execute();
        return $this->categoriesStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's an array with all available categories by store view ID.
     *
     * @param integer $storeViewId The store view ID to return the categories for
     *
     * @return array The available categories for the passed store view ID
     */
    public function findAllByStoreView($storeViewId)
    {
        // try to load the categories and return them
        $this->categoriesByStoreViewStmt->execute(array(MemberNames::STORE_ID => $storeViewId));
        return $this->categoriesByStoreViewStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function findAllRootCategories()
    {

        // try to load the categories
        $this->rootCategoriesStmt->execute();

        // initialize the array with the store code as key
        $rootCategories = array();

        // load the available root categories
        $availableRootCategories = $this->rootCategoriesStmt->fetchAll(\PDO::FETCH_ASSOC);

        // prepare the array with the root categories
        foreach ($availableRootCategories as $category) {
            $rootCategories[$category[MemberNames::CODE]] = $category;
        }

        // append the root categories to the cache
        return $rootCategories;
    }
}
