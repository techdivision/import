<?php

/**
 * TechDivision\Import\Repositories\LinkTypeRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load link type data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LinkTypeRepository extends AbstractRepository implements LinkTypeRepositoryInterface
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

        // initialize the prepared statements
        $this->linkTypeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::LINK_TYPES));
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

            // load the available link types
            $availableLinkTypes = $this->linkTypeStmt->fetchAll(\PDO::FETCH_ASSOC);

            // prepare the link types => we need the code as key
            foreach ($availableLinkTypes as $linkType) {
                $linkTypes[$linkType[MemberNames::CODE]] = $linkType;
            }

            // append the link types to the cache
            $this->cache[__METHOD__] = $linkTypes;
        }

        // return the link types from the cache
        return $this->cache[__METHOD__];
    }
}
