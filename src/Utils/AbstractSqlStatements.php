<?php

/**
 * TechDivision\Import\Utils\AbstractSqlStatements
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

namespace TechDivision\Import\Utils;

/**
 * Abstract utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractSqlStatements implements SqlStatementsInterface
{

    /**
     * The initializes SQL statements.
     *
     * @var array
     */
    protected $preparedStatements = array();

    /**
     * Returns the SQL statement with the passed name.
     *
     * @param string $key The key of the SQL statement to return
     *
     * @return string The SQL statement
     * @throws \Exception Is thrown, if the SQL statement with the passed key cannot be found
     */
    public function find($key)
    {

        // try to find the SQL statement with the passed key
        if (isset($this->preparedStatements[$key])) {
            return $this->preparedStatements[$key];
        }

        // throw an exception if NOT available
        throw new \Exception(sprintf('Can\'t find SQL statement with key %s', $key));
    }
}
