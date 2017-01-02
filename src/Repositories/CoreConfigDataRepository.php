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
     * The statement to load the configuration.
     *
     * @var \PDOStatement
     */
    protected $coreConfigDataStmt;

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
        $this->coreConfigDataStmt->execute();
        return $this->coreConfigDataStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
