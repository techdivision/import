<?php

/**
 * Importer\Csv\Actions\Observers\Product\ProductCategoryObserver
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

namespace TechDivision\Import\Observers\Product;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductCategoryObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // extract the category trees and try to import the data
        $trees = explode('|', $row[$headers[ColumnKeys::CATEGORIES]]);
        foreach ($trees as $tree) {
            // perpare the categories for usage in the SQL IN clause
            $cats = explode('/', $tree);
            array_walk($cats, function(&$cat) { $cat = "'$cat'"; });

            // load the categories
            $categories = $this->getCategoriesByValues($cats);

            // iterate over the found entities and relate them with the product
            foreach ($categories as $category) {
                $this->persistProductCategory(array($category[MemberNames::ENTITY_ID], $lastEntityId, 0));
            }
        }

        // returns the row
        return $row;
    }

    /**
     * Persist's the passed product category data and return's the ID.
     *
     * @param array $productWebsite The product category data to persist
     *
     * @return void
     */
    public function persistProductCategory($productCategory)
    {
        $this->getSubject()->persistProductCategory($productCategory);
    }

    /**
     * Return's an array of the categories with the passed values.
     *
     * @param array The names of the categories to return
     *
     * @return array The array with all available stores
     */
    public function getCategoriesByValues($values)
    {
        return $this->getSubject()->getCategoriesByValues($values);
    }
}
