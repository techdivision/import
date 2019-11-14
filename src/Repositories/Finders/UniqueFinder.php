<?php

/**
 * TechDivision\Import\Repositories\Finders\UniqueFinder
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

namespace TechDivision\Import\Repositories\Finders;

/**
 * A simple finder implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UniqueFinder implements FinderInterface
{

    /**
     * The unique key of the prepared statement that has to be executed.
     *
     * @var string
     */
    protected $key;

    /**
     * The entity's primary key name.
     *
     * @var string
     */
    protected $primaryKeyName;

    /**
     * The finder's entity name.
     *
     * @var string
     */
    protected $entityName;

    /**
     * The prepared statement.
     *
     * @var \PDOStatement
     */
    protected $preparedStatement;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \PDOStatement $preparedStatement The prepared statement
     * @param string        $key               The unqiue key of the prepared statement that has to be executed.
     * @param string        $primaryKeyName    The entity's primary key
     * @param string        $entityName        The finder's entity name
     */
    public function __construct(\PDOStatement $preparedStatement, $key, $primaryKeyName, $entityName)
    {
        $this->preparedStatement = $preparedStatement;
        $this->primaryKeyName = $primaryKeyName;
        $this->entityName = $entityName;
        $this->key = $key;
    }

    /**
     * Return's the finder's unique key.
     *
     * @return string The unique key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Return's the entity's primary key name.
     *
     * @return string The entity's primary key name
     */
    public function getPrimaryKeyName()
    {
        return $this->primaryKeyName;
    }

    /**
     * Return's the finder's entity name.
     *
     * @return string The finder's entity name
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Executes the finder with the passed parameters.
     *
     * @param array The finder params
     *
     * @return array The finder result
     */
    public function find(array $params = array())
    {

        // execute the prepared statement and return the results
        $this->preparedStatement->execute($params);
        return $this->preparedStatement->fetch(\PDO::FETCH_ASSOC);
    }
}
