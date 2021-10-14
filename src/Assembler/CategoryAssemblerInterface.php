<?php

/**
 * TechDivision\Import\Assembler\CategoryAssemblerInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Assembler;

/**
 *Interface for catagory data assembler implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CategoryAssemblerInterface
{

    /**
     * Returns an array with the available categories and their
     * resolved path as keys.
     *
     * @return array The array with the categories
     */
    public function getCategoriesWithResolvedPath();

    /**
     * Return's an array with the available categories and their resolved path
     * as keys by store view ID.
     *
     * @param integer $storeViewId The store view ID to return the categories for
     *
     * @return array The available categories for the passed store view ID
     */
    public function getCategoriesWithResolvedPathByStoreView($storeViewId);
}
