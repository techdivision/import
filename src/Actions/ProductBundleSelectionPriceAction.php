<?php

/**
 * TechDivision\Import\Repositories\ProductBundleSelectionPriceAction
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

namespace TechDivision\Import\Actions;

/**
 * A SLSB providing repository functionality for product bundle selection price CRUD actions.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductBundleSelectionPriceAction extends AbstractAction
{

    /**
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return string The last inserted ID
     */
    public function persist($row)
    {
        return $this->getPersistProcessor()->execute($row);
    }
}
