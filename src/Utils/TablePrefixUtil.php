<?php

/**
 * TechDivision\Import\Utils\TablePrefixUtil
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

namespace TechDivision\Import\Utils;

use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Utility class for table prefix handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class TablePrefixUtil implements TablePrefixUtilInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the prefixed table name.
     *
     * @param string $tableName The table name to prefix
     *
     * @return string The prefixed table name
     * @throws \Exception Is thrown if the table name can't be prefixed
     */
    public function getPrefixedTableName($tableName)
    {

        // try to load the table prefix from the configuration
        if ($tablePrefix = $this->configuration->getDatabase()->getTablePrefix()) {
            $tableName = sprintf('%s%s', $tablePrefix, $tableName);
        }

        // return the prefixed table name
        return $tableName;
    }

    /**
     * Compiles the passed SQL statement.
     *
     * @param string $statement The SQL statement to compile
     *
     * @return string The compiled SQL statement
     */
    public function compile($statement)
    {
        return preg_replace_callback(sprintf('/\$\{%s:(.*)\}/U', TablePrefixUtilInterface::TOKEN), function (array $matches) {
            return $this->getPrefixedTableName($matches[1]);
        }, $statement);
    }
}
