<?php

/**
 * TechDivision\Import\Repositories\CoreConfigDataRepository
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

use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Connection\ConnectionInterface;

/**
 * Repository implementation to load the Magento 2 configuration data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CoreConfigDataRepository extends AbstractRepository implements CoreConfigDataRepositoryInterface
{

    /**
     * The UID generator for the core config data.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    protected $coreConfigDataUidGenerator;

    /**
     * The statement to load the configuration.
     *
     * @var \PDOStatement
     */
    protected $coreConfigDataStmt;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     *
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface          $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection                 The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository     The SQL repository instance
     */
    public function __construct(
        GeneratorInterface $coreConfigDataUidGenerator,
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository
    ) {
        parent::__construct($connection, $sqlStatementRepository);
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
    }

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->coreConfigDataStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CORE_CONFIG_DATA));
    }

    /**
     * Return's an array with the Magento 2 configuration.
     *
     * @return array The configuration
     */
    public function findAll()
    {

        // prepare the core configuration data
        $coreConfigDatas = array();

        // execute the prepared statement
        $this->coreConfigDataStmt->execute();

        // load the available core config data
        $availableCoreConfigDatas = $this->coreConfigDataStmt->fetchAll(\PDO::FETCH_ASSOC);

        // create the array with the resolved category path as keys
        foreach ($availableCoreConfigDatas as $coreConfigData) {
            // prepare the unique identifier
            $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);
            // append the config data value with the unique identifier
            $coreConfigDatas[$uniqueIdentifier] = $coreConfigData;
        }

        // return array with the configuration data
        return $coreConfigDatas;
    }
}
