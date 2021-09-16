<?php

/**
 * TechDivision\Import\Repositories\StoreWebsiteRepository
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
 * Repository implementation to load store website data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StoreWebsiteRepository extends AbstractRepository implements StoreWebsiteRepositoryInterface
{

    /**
     * The prepared statement to load the store websites.
     *
     * @var \PDOStatement
     */
    protected $storeWebsitesStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->storeWebsitesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::STORE_WEBSITES));
    }

    /**
     * Return's an array with the available store websites and their
     * code as keys.
     *
     * @return array The array with all available store websites
     */
    public function findAll()
    {

        // initialize the array with the available store websites
        $storeWebsites = array();

        // execute the prepared statement
        $this->storeWebsitesStmt->execute();

        // load the available store websites
        $availableStoreWebsites = $this->storeWebsitesStmt->fetchAll();

        // fetch the store websites and assemble them as array with the codes as key
        foreach ($availableStoreWebsites as $storeWebsite) {
            $storeWebsites[$storeWebsite[MemberNames::CODE]] = $storeWebsite;
        }

        // return the array with the store websites
        return $storeWebsites;
    }
}
