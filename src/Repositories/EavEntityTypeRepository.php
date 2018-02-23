<?php

/**
 * TechDivision\Import\Repositories\EavEntityTypeRepository
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

use TechDivision\Import\Utils\MemberNames;

/**
 * Repository implementation to load the EAV entity type data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavEntityTypeRepository extends AbstractRepository implements EavEntityTypeRepositoryInterface
{

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * The statement to load the available EAV entity types.
     *
     * @var \PDOStatement
     */
    protected $eavEntityTypeStmt;

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
        $this->eavEntityTypeStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::EAV_ENTITY_TYPES));
    }

    /**
     * Return's an array with all available EAV entity types with the entity type code as key.
     *
     * @return array The available link types
     */
    public function findAll()
    {

        // initialize the array for the EAV entity types
        $eavEntityTypes = array();

        // try to load the EAV entity types
        $this->eavEntityTypeStmt->execute();

        // prepare the EAV entity types => we need the entity type code as key
        foreach ($this->eavEntityTypeStmt->fetchAll(\PDO::FETCH_ASSOC) as $eavEntityType) {
            $eavEntityTypes[$eavEntityType[MemberNames::ENTITY_TYPE_CODE]] = $eavEntityType;
        }

        // return the array with the EAV entity types
        return $eavEntityTypes;
    }
}
