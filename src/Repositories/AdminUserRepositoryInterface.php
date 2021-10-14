<?php

/**
 * TechDivision\Import\Repositories\AdminUserRepositoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

/**
 * Repository implementation to load admin user data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface AdminUserRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with all available admin users.
     *
     * @return array The available admin users
     */
    public function findAll();

    /**
     * Load's and return's the admin user with the passed username.
     *
     * @param string $username The username of the admin user to return
     *
     * @return array|null The admin user with the passed username
     */
    public function findOneByUsername($username);
}
