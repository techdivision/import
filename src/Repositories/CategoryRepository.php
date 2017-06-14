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
        $this->categoriesStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::CATEGORIES));
        $this->rootCategoriesStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::ROOT_CATEGORIES));
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
        foreach ($this->rootCategoriesStmt->fetchAll(\PDO::FETCH_ASSOC) as $category) {
            $rootCategories[$category[MemberNames::CODE]] = $category;
        }

        // append the root categories to the cache
        return $rootCategories;
    }
}
