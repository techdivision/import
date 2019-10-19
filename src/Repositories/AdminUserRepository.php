<?php

/**
 * TechDivision\Import\Repositories\AdminUserRepository
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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Repository implementation to load admin user data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdminUserRepository extends AbstractRepository implements AdminUserRepositoryInterface
{

    /**
     * The prepared statement to load an user by it's username.
     *
     * @var \PDOStatement
     */
    protected $adminUserByUsernameStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->adminUsersStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::ADMIN_USERS));
        $this->adminUserByUsernameStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::ADMIN_USER_BY_USERNAME));
    }

    /**
     * Return's an array with all available admin users.
     *
     * @return array The available admin users
     */
    public function findAll()
    {
        // try to load the categories
        $this->adminUsersStmt->execute();
        return $this->adminUsersStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the admin user with the passed username.
     *
     * @param string $username The username of the admin user to return
     *
     * @return array|null The admin user with the passed username
     */
    public function findOneByUsername($username)
    {

        // the parameters of the admin user to load
        $params = array(MemberNames::USERNAME => $username);

        // load and return the admin user with the passed parameters
        $this->adminUserByUsernameStmt->execute($params);
        return $this->adminUserByUsernameStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
