<?php

/**
 * TechDivision\Import\Actions\Processors\Batch\ProductBundleOptionPersistBatchProcessor
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

namespace TechDivision\Import\Actions\Processors\Batch;

use TechDivision\Import\Utils\SqlStatements;

/**
 * The product persist batch processor implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductBundleOptionPersistBatchProcessor extends AbstractPersistBatchProcessor
{

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Actions\Processors\Batch\AbstractPersistBatchProcessor::getNumberOfPlaceholders()
     */
    protected function getNumberOfPlaceholders()
    {
        return 4;
    }

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Actions\Processors\Batch\AbstractPersistBatchProcessor::getStatement()
     */
    protected function getStatement()
    {
        return SqlStatements::CREATE_PRODUCT_BUNDLE_OPTION;
    }

    /**
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return string The last inserted ID
     */
    public function execute($row)
    {
        $this->addToStack($row);
        return (string) $this->getStackSize();
    }
}
