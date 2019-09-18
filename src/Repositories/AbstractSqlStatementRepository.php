<?php

/**
 * TechDivision\Import\Repositories\AbstractSqlStatementRepository
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

namespace TechDivision\Import\Repositories;

/**
 * Abstract repository class for the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractSqlStatementRepository implements SqlStatementRepositoryInterface
{

    /**
     * The initializes SQL statements.
     *
     * @var array
     */
    private $preparedStatements = array();

    /**
     * The array with the compiler instances.
     *
     * @var \IteratorAggregate<\TechDivision\Import\Utils\SqlCompilerInterface>
     */
    private $compilers = array();

    /**
     * Initializes the SQL statement repository with the primary key and table prefix utility.
     *
     * @param \IteratorAggregate<\TechDivision\Import\Utils\SqlCompilerInterface> $compilers The array with the compiler instances
     */
    public function __construct(\IteratorAggregate $compilers)
    {
        $this->compilers = $compilers;
    }

    /**
     * Returns the SQL statement with the passed ID.
     *
     * @param string $id The ID of the SQL statement to return
     *
     * @return string The SQL statement
     * @throws \Exception Is thrown, if the SQL statement with the passed key cannot be found
     */
    public function load($id)
    {

        // try to find the SQL statement with the passed key
        if (isset($this->preparedStatements[$id])) {
            return $this->preparedStatements[$id];
        }

        // throw an exception if NOT available
        throw new \Exception(sprintf('Can\'t find SQL statement with ID %s', $id));
    }

    /**
     * Compiles the passed SQL statements.
     *
     * @param array $statements The SQL statements to compile
     *
     * @return void
     */
    protected function compile(array $statements)
    {

        // iterate over all statements and compile them
        foreach ($statements as $key => $statement) {
            // compile the statement
            foreach ($this->compilers as $compiler) {
                $statement = $compiler->compile($statement);
            }
            // add the comiled statemnent to the list
            $this->preparedStatements[$key] = $statement;
        }
    }
}
