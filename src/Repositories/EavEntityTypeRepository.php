<?php

/**
 * TechDivision\Import\Repositories\EavEntityTypeRepository
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
 * Repository implementation to load the EAV entity type data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavEntityTypeRepository extends AbstractRepository implements EavEntityTypeRepositoryInterface
{

    /**
     * The statement to load the available EAV entity types.
     *
     * @var \PDOStatement
     */
    protected $eavEntityTypeStmt;

    /**
     * The prepared statement to load an existing EAV entity type by its entity type code.
     *
     * @var \PDOStatement
     */
    protected $eavEntityTypeByEntityTypeACodeStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->eavEntityTypeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ENTITY_TYPES));
        $this->eavEntityTypeByEntityTypeACodeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::EAV_ENTITY_TYPE_BY_ENTITY_TYPE_CODE));
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

        // load the available EAV entity types
        $availableEntityTypes = $this->eavEntityTypeStmt->fetchAll(\PDO::FETCH_ASSOC);

        // prepare the EAV entity types => we need the entity type code as key
        foreach ($availableEntityTypes as $eavEntityType) {
            $eavEntityTypes[$eavEntityType[MemberNames::ENTITY_TYPE_CODE]] = $eavEntityType;
        }

        // return the array with the EAV entity types
        return $eavEntityTypes;
    }

    /**
     * Return's an EAV entity type with the passed entity type code.
     *
     * @param string $entityTypeCode The code of the entity type to return
     *
     * @return array The entity type with the passed entity type code
     */
    public function findOneByEntityTypeCode($entityTypeCode)
    {
        // load and return the EAV attribute with the passed params
        $this->eavEntityTypeByEntityTypeACodeStmt->execute(array(MemberNames::ENTITY_TYPE_CODE => $entityTypeCode));
        return $this->eavEntityTypeByEntityTypeACodeStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
