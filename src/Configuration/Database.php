<?php

/**
 * TechDivision\Import\Configuration\Database
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

namespace TechDivision\Import\Configuration;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Database implements DatabaseInterface
{

    /**
     * The PDO DSN to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("pdo-dsn")
     */
    protected $dsn;

    /**
     * The DB username to use.
     *
     * @var string
     * @Type("string")
     */
    protected $username;

    /**
     * The DB password to use.
     *
     * @var string
     * @Type("string")
     */
    protected $password;

    /**
     * Return's the PDO DSN to use.
     *
     * @return string The PDO DSN
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * Return's the DB username to use.
     *
     * @return string The DB username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return's the DB password to use.
     *
     * @return string The DB password
     */
    public function getPassword()
    {
        return $this->password;
    }
}
