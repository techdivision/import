<?php

/**
 * TechDivision\Import\Repositories\LinkTypeRepository
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

use TechDivision\Import\Utils\MemberNames;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LinkTypeRepository extends AbstractRepository
{

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * The statement to load the available link types.
     *
     * @var \PDOStatement
     */
    protected $linkTypeStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->linkTypeStmt = $this->getConnection()->prepare($utilityClassName::LINK_TYPES);
    }

    /**
     * Return's an array with all available link types with the link type code as key.
     *
     * @return array The available link types
     */
    public function findAll()
    {

        // query whether or not we've already loaded the value
        if (!isset($this->cache[__METHOD__])) {
            // try to load the link types
            $this->linkTypeStmt->execute();

            // initialize the array for the link types
            $linkTypes = array();

            // prepare the link types => we need the code as key
            foreach ($this->linkTypeStmt->fetchAll() as $linkType) {
                $linkTypes[$linkType[MemberNames::CODE]] = $linkType;
            }

            // append the link types to the cache
            $this->cache[__METHOD__] = $linkTypes;
        }

        // return the link types from the cache
        return $this->cache[__METHOD__];
    }
}
