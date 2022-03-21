<?php

/**
 * TechDivision\Import\Repositories\CategoryRepository
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The statement to load the category by store view.
     *
     * @var \PDOStatement
     */
    protected $categoriesByStoreViewStmt;

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
