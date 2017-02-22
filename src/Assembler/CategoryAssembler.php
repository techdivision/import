<?php

/**
 * TechDivision\Import\Assembler\CategoryAssembler
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

namespace TechDivision\Import\Assembler;

use TechDivision\Import\Utils\MemberNames;

/**
 * Repository implementation to assemble category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryAssembler
{

    /**
     * The repository to access categories.
     *
     * @var \TechDivision\Import\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * The repository to access category varchar values.
     *
     * @var \TechDivision\Import\Repositories\CategoryVarcharRepository
     */
    protected $categoryVarcharRepository;

    /**
     * Initialize the assembler with the passed instances.
     *
     * @param \TechDivision\Import\Repositories\CategoryRepository $categoryRepository The repository to access categories
     * @param \TechDivision\Import\Repositories\CategoryVarcharRepository $categoryVarcharRepository The repository instance
     */
    public function __construct($categoryRepository, $categoryVarcharRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryVarcharRepository = $categoryVarcharRepository;
    }

    /**
     * Returns an array with the available categories and their
     * resolved path as keys.
     *
     * @return array The array with the categories
     */
    public function getCategoriesWithResolvedPath()
    {

        // prepare the categories
        $categories = array();

        // create the array with the resolved category path as keys
        foreach ($this->categoryRepository->findAll() as $category) {
            // expload the entity IDs from the category path
            $entityIds = explode('/', $category[MemberNames::PATH]);

            // cut-off the root category
            array_shift($entityIds);

            // continue with the next category if no entity IDs are available
            if (sizeof($entityIds) === 0) {
                continue;
            }

            // initialize the array for the path elements
            $path = array();
            foreach ($this->categoryVarcharRepository->findAllByEntityIds($entityIds) as $cat) {
                $path[] = $cat[MemberNames::VALUE];
            }

            // append the catogory with the string path as key
            $categories[implode('/', $path)] = $category;
        }

        // return array with the categories
        return $categories;
    }
}
