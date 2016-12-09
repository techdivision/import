<?php

/**
 * TechDivision\Import\Actions\Processors\AbstractBaseProcessor
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

/**
 * An abstract processor implementation provide basic CRUD functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractBaseProcessor extends AbstractProcessor
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
    abstract protected function getStatement();

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
     * Implements the CRUD functionality the processor is responsible for,
     * can be one of CREATE, READ, UPDATE or DELETE a entity.
     *
     * @param array $row The data to handle
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
