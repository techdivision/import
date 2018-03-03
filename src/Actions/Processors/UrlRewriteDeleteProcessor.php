<?php

/**
 * TechDivision\Import\Actions\Processors\UrlRewriteDeleteProcessor
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

namespace TechDivision\Import\Actions\Processors;

use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * The URL rewrite delete processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteDeleteProcessor extends AbstractDeleteProcessor
{

    /**
     * Return's the array with the SQL statements that has to be prepared.
     *
     * @return array The SQL statements to be prepared
     * @see \TechDivision\Import\Actions\Processors\AbstractBaseProcessor::getStatements()
     */
    protected function getStatements()
    {
        return array(
            SqlStatementKeys::DELETE_URL_REWRITE => $this->loadStatement(SqlStatementKeys::DELETE_URL_REWRITE),
            SqlStatementKeys::DELETE_URL_REWRITE_BY_SKU => $this->loadStatement(SqlStatementKeys::DELETE_URL_REWRITE_BY_SKU),
            SqlStatementKeys::DELETE_URL_REWRITE_BY_PATH => $this->loadStatement(SqlStatementKeys::DELETE_URL_REWRITE_BY_PATH),
            SqlStatementKeys::DELETE_URL_REWRITE_BY_CATEGORY_ID => $this->loadStatement(SqlStatementKeys::DELETE_URL_REWRITE_BY_CATEGORY_ID)
        );
    }
}
