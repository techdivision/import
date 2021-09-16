<?php

/**
 * TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface
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

/**
 * Interface for a EAV entity type data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavEntityTypeRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with all available EAV entity types with the entity type code as key.
     *
     * @return array The available link types
     */
    public function findAll();

    /**
     * Return's an EAV entity type with the passed entity type code.
     *
     * @param string $entityTypeCode The code of the entity type to return
     *
     * @return array The entity type with the passed entity type code
     */
    public function findOneByEntityTypeCode($entityTypeCode);
}
