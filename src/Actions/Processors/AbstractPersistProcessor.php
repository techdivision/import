<?php

/**
 * TechDivision\Import\Actions\Processors\ProductPersistProcessor
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

namespace TechDivision\Import\Actions\Processors;

/**
 * An abstract respository implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
abstract class AbstractPersistProcessor extends AbstractProcessor
{

    /**
     * The prepared statement.
     *
     * @var \PDOStatement
     */
    protected $preparedStatement;

    /**
     * Return's the SQL statement that has to be prepared.
     *
     * @return string The SQL statement
     */
    protected abstract function getStatement();

    /**
     * Set's the prepared statement.
     *
     * @param \PDOStatement $preparedStatement The prepared statement
     *
     * @return void
     */
    protected function setPreparedStatement(\PDOStatement $preparedStatement)
    {
        $this->preparedStatement = $preparedStatement;
    }

    /**
     * Return's the prepared statement.
     *
     * @return \PDOStatement The prepared statement
     */
    protected function getPreparedStatement()
    {
        return $this->preparedStatement;
    }

    /**
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return void
     */
    public function execute($row)
    {
        $this->getPreparedStatement()->execute($row);
    }

    /**
     * Initializes the proceessor with the prepared statements.
     *
     * @return void
     */
    public function init()
    {
        $this->setPreparedStatement($this->getConnection()->prepare($this->getStatement()));
    }
}
