<?php

/**
 * TechDivision\Import\Assembler\CategoryAssembler
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Serializer\SerializerInterface;
use TechDivision\Import\Repositories\CategoryRepository;
use TechDivision\Import\Repositories\CategoryVarcharRepository;

/**
 * Repository implementation to assemble category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryAssembler implements CategoryAssemblerInterface
{

    /**
     * The serialize instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    protected $serializer;

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
     * @param \TechDivision\Import\Repositories\CategoryRepository        $categoryRepository        The repository to access categories
     * @param \TechDivision\Import\Repositories\CategoryVarcharRepository $categoryVarcharRepository The repository instance
     * @param \TechDivision\Import\Serializer\SerializerInterface         $serializer                The serializer instance
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryVarcharRepository $categoryVarcharRepository,
        SerializerInterface $serializer
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryVarcharRepository = $categoryVarcharRepository;
        $this->serializer = $serializer;
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

        // load the categories from the database
        $availableCategories = $this->categoryRepository->findAll();

        // create the array with the resolved category path as keys
        foreach ($availableCategories as $category) {
            // expload the entity IDs from the category path
            $entityIds =  $this->serializer->explode($category[MemberNames::PATH]);

            // continue if nothing to explode
            if ($entityIds === null) {
                continue;
            }

            // cut-off the root category
            array_shift($entityIds);

            // continue with the next category if no entity IDs are available
            if (sizeof($entityIds) === 0) {
                continue;
            }

            // initialize the array for the path elements
            $path = array();
            foreach ($entityIds as $entityId) {
                $cat = $this->categoryVarcharRepository->findByEntityId($entityId);
                if ($cat && isset($cat[MemberNames::VALUE])) {
                    $path[] = $cat[MemberNames::VALUE];
                }
            }

            // append the catogory with the string path as key
            $categories[$this->serializer->implode($path)] = $category;
        }

        // return array with the categories
        return $categories;
    }


    /**
     * Return's an array with the available categories and their resolved path
     * as keys by store view ID.
     *
     * @param integer $storeViewId The store view ID to return the categories for
     *
     * @return array The available categories for the passed store view ID
     */
    public function getCategoriesWithResolvedPathByStoreView($storeViewId)
    {
        // prepare the categories
        $categories = array();

        // load the categories from the database
        $availableCategories = $this->categoryRepository->findAllByStoreView($storeViewId);

        // create the array with the resolved category path as keys
        foreach ($availableCategories as $category) {
            // expload the entity IDs from the category path
            $entityIds = $this->serializer->explode($category[MemberNames::PATH]);

            // cut-off the root category
            array_shift($entityIds);

            // continue with the next category if no entity IDs are available
            if (sizeof($entityIds) === 0 || !isset($category[MemberNames::NAME])) {
                continue;
            }

            // initialize the array for the path elements
            $path = array();
            foreach ($entityIds as $entityId) {
                $cat = $this->categoryVarcharRepository->findByEntityId($entityId);
                if ($cat && isset($cat[MemberNames::VALUE])) {
                    $path[] = $cat[MemberNames::VALUE];
                }
            }

            // append the catogory with the string path as key
            $categories[$this->serializer->implode($path)] = $category;
        }

        // return array with the categories
        return $categories;
    }
}
