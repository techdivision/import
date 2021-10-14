<?php

/**
 * TechDivision\Import\Repositories\CategoryRepositoryInterface
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

/**
 * Interface for a category data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function findAll();

    /**
     * Return's an array with all available categories by store view ID.
     *
     * @param integer $storeViewId The store view ID to return the categories for
     *
     * @return array The available categories for the passed store view ID
     */
    public function findAllByStoreView($storeViewId);

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function findAllRootCategories();
}
