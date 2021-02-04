<?php

/**
 * TechDivision\Import\Actions\Processors\ImportHistoryCreateProcessor
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Actions\Processors;

use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * The import history create processor implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Actions\Processors\GenericIdentifierProcessor
 */
class ImportHistoryCreateProcessor extends AbstractCreateProcessor
{

    /**
     * Return's the array with the SQL statements that has to be prepared.
     *
     * @return array The SQL statements to be prepared
     * @see \TechDivision\Import\Actions\Processors\AbstractBaseProcessor::getStatements()
     */
    protected function getStatements()
    {
        return array(SqlStatementKeys::CREATE_IMPORT_HISTORY => $this->loadStatement(SqlStatementKeys::CREATE_IMPORT_HISTORY));
    }

    /**
     * Persist's the passed row.
     *
     * @param array       $row                  The row to persist
     * @param string|null $name                 The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return string The last inserted ID
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null)
    {
        parent::execute($row, $name);
        return $this->getConnection()->lastInsertId();
    }
}
