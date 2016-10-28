<?php

/**
 * TechDivision\Import\Utils\Ee\SqlStatements
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

namespace TechDivision\Import\Utils\Ee;

use TechDivision\Import\Utils\SqlStatements as FallbackStatements;

/**
 * A SSB providing process registry functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class SqlStatements extends FallbackStatements
{

    /**
     * The SQL statement to load the categories of a product.
     *
     * @var string
     */
    const CATEGORIES = 'SELECT t1.*
                          FROM catalog_category_entity AS t1
                    INNER JOIN catalog_category_entity_varchar AS t2
                            ON t2.value IN(?)
                           AND t2.row_id = t1.row_id';
}