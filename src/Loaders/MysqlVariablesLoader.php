<?php

/**
 * TechDivision\Import\Loaders\MySqlVariablesLoader
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

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for attribute sets.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MysqlVariablesLoader implements LoaderInterface
{

    /**
     * The key for the variable name.
     *
     * @var string
     */
    const VARIABLE_NAME = 'Variable_name';

    /**
     * The key for the variable value.
     *
     * @var string
     */
    const VALUE = 'Value';

    /**
     * The connection instance.
     *
     * @var \TechDivision\Import\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The possible variable scopes.
     *
     * @var array
     */
    protected $variableScopes = array('GLOBAL', 'SESSION');

    /**
     * The MySQL variable scope (one of GLOBAL or SESSION).
     *
     * @var string
     */
    protected $variableScope;

    /**
     * The MySQL variable name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * The recommendations for critically MySQL variables.
     *
     * @var array
     */
    protected $recommendations = array(
        'innodb_flush_log_at_trx_commit' => array(
            'severity' => array(0 => 0, 1 => 2, 2 => 0),
            'description' => 'Your setting for may result in a significantly slower performance. Consider to switch this value to 0 or 2 to improve performance. Read more about that topic on the MySQL website https://dev.mysql.com/doc/refman/5.7/en/innodb-parameters.html#sysvar_innodb_flush_log_at_trx_commit. '
        )
    );

    /**
     * The available severity mappings.
     *
     * @var array
     */
    protected $severityMappings = array(
        0 => 'white',
        1 => 'yellow',
        2 => 'red'
    );

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Connection\ConnectionInterface $connection    The connection instance
     * @param string                                              $variableScope The MySQL variable scope
     * @param string                                              $variableNmae  The MySQL variable name
     */
    public function __construct(ConnectionInterface $connection, $variableScope = 'GLOBAL', $variableName = 'innodb%')
    {

        // set the connection and the variable name
        $this->connection = $connection;

        // set the variable name and scope
        $this->setVariableName($variableName);
        $this->setVariableScope($variableScope);
    }

    /**
     * Loads and returns data the custom validation data.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the validations from
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(SubjectConfigurationInterface $configuration = null)
    {

        // initialize the array for the rows
        $rows = array();

        // prepare the statement
        $statement = sprintf("SHOW %s VARIABLES LIKE %s", $this->getVariableScope(), $this->getVariableName());

        // load and assemble the row
        foreach ($this->getConnection()->query($statement) as $value) {
            if ($row = $this->prepareRow($value)) {
                $rows[] = $row;
            }
        }

        // return the rows
        return $rows;
    }

    /**
     * Initialize the arary with the table row.
     *
     * @param array $value The status variable loaded from the DB
     *
     * @return string[] The array with the row ready to be rendered
     */
    protected function prepareRow(array $value)
    {

        // query whether or not a recommendation for the variable is available
        if (isset($this->recommendations[$variableName = $value[self::VARIABLE_NAME]])) {
            // if yes, load the recommendation
            $recommendation = $this->recommendations[$variableName];
            // load the severity of the recommendation
            $severity = $recommendation['severity'][$val = $value[self::VALUE]];
            // if severity is at least on a warning level
            if ($severity >= 1) {
                // format the value
                $mappedValue = sprintf('<bg=%s>%s</>', $this->severityMappings[$severity], $val);
                // return the row with the formatted value
                return array(
                    'Variable'    => $variableName,
                    'Value'       => $mappedValue,
                    'Description' => $recommendation['description']
                );
            }
        }
    }

    /**
     * Set's the MySQL variable name.
     *
     * @param string $variableName The MySQL variable name
     *
     * @return void
     */
    protected function setVariableName($variableName)
    {
        $this->variableName = $this->getConnection()->quote($variableName);
    }

    /**
     * Return's the quoted MySQL variable name.
     *
     * @return string The MySQL variable name
     */
    protected function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Set's the the MySQL variable scope.
     *
     * @param string $variableScope The MySQL variable scope to use
     *
     * @return void
     * @throws \InvalidArgumentException Is thrown if the passed variable is invalid
     */
    protected function setVariableScope($variableScope)
    {
        if (in_array($variableScope, $this->variableScopes)) {
            $this->variableScope = $variableScope;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument %s passed (required one of %s',
                    $variableScope,
                    implode(', ', $this->variableScopes)
                )
            );
        }
    }

    /**
     * Return's the MySQL variable scope.
     *
     * @return string The MySQL variable scope
     */
    protected function getVariableScope()
    {
        return $this->variableScope;
    }

    /**
     * Returns the coonection instance.
     *
     * @return \TechDivision\Import\Connection\ConnectionInterface The connection instance
     */
    protected function getConnection()
    {
        return $this->connection;
    }
}
