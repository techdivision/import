<?php

/**
 * TechDivision\Import\Configuration\DatabaseConfigurationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * Interface for the database configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface DatabaseConfigurationInterface
{

    /**
     * Return's the database identifier for this database connection.
     *
     * @return string The database identifier
     */
    public function getId();

    /**
     * Return's the PDO DSN to use.
     *
     * @return string The PDO DSN
     */
    public function getDsn();

    /**
     * Return's the DB username to use.
     *
     * @return string The DB username
     */
    public function getUsername();

    /**
     * Return's the DB password to use.
     *
     * @return string The DB password
     */
    public function getPassword();

    /**
     * Return's the flag to signal that this is the default datasource or not.
     *
     * @return boolean TRUE if this is the default datasource, else FALSE
     */
    public function isDefault();
}
