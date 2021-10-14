<?php

/**
 * TechDivision\Import\Repositories\CustomerGroupRepository
 *
 * PHP version 7
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * The default repository implementation for loading customer groups.
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */
class CustomerGroupRepository extends AbstractRepository implements CustomerGroupRepositoryInterface
{

    /**
     * The prepared statement to load the customer groups.
     *
     * @var \PDOStatement
     */
    protected $customerGroupsStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
        // initialize the prepared statements
        $this->customerGroupsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CUSTOMER_GROUPS));
    }

    /**
     * Returns an array with the available customer groups and their code as keys.
     *
     * @return array The array with the customer groups
     */
    public function findAll()
    {

        // initialize the array for the customer groups
        $customerGroups = [];

        // execute the prepared statement
        $this->customerGroupsStmt->execute();

        // load the available customer groups
        $availableCustomerGroups = $this->customerGroupsStmt->fetchAll();

        // fetch the customer groups and assemble them as array with the codes as key
        foreach ($availableCustomerGroups as $customerGroup) {
            $customerGroups[$customerGroup[MemberNames::CUSTOMER_GROUP_CODE]] = $customerGroup;
        }

        // return the customer groups
        return $customerGroups;
    }
}
