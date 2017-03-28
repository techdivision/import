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

use TechDivision\Import\Utils\Generators\GeneratorInterface;

/**
 * Repository implementation to load the Magento 2 configuration data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CoreConfigDataRepository extends AbstractRepository
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
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \PDO|null                                                $connection                 The PDO connection instance
     * @param string|null                                              $utilityClassName           The utility class name
     */
    public function __construct(
        GeneratorInterface $coreConfigDataUidGenerator,
        \PDO $connection = null,
        $utilityClassName = null
    ) {
        parent::__construct($connection, $utilityClassName);
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
    }

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
        $this->coreConfigDataStmt = $this->getConnection()->prepare($utilityClassName::CORE_CONFIG_DATA);
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

        // create the array with the resolved category path as keys
        foreach ($this->coreConfigDataStmt->fetchAll(\PDO::FETCH_ASSOC) as $coreConfigData) {
            // prepare the unique identifier
            $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);
            // append the config data value with the unique identifier
            $coreConfigDatas[$uniqueIdentifier] = $coreConfigData;
        }

        // return array with the configuration data
        return $coreConfigDatas;
    }
}
