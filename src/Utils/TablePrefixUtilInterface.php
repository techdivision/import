<?php

/**
 * TechDivision\Import\Utils\TablePrefixUtilInterface
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

/**
 * Interface for table prefix utility implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface TablePrefixUtilInterface extends SqlCompilerInterface
{

    /**
     * The token used to identifiy a primary key column.
     *
     * @var string
     */
    const TOKEN = 'table';

    /**
     * Returns the prefixed table name.
     *
     * @param string $tableName The table name to prefix
     *
     * @return string The prefixed table name
     * @throws \Exception Is thrown if the table name can't be prefixed
     */
    public function getPrefixedTableName($tableName);
}
