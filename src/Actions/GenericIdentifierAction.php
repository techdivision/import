<?php

/**
 * TechDivision\Import\Actions\GenericIdentifierAction
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
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Actions;

use TechDivision\Import\Utils\EntityStatus;

/**
 * An action implementation that provides CRUD functionality and returns the ID of
 * the persisted entity for the `update` and `create` methods.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class GenericIdentifierAction extends GenericAction implements IdentifierActionInterface
{

    /**
     * Helper method that create/update the passed entity, depending on
     * the entity's status.
     *
     * @param array       $row  The entity data to create/update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persist(array $row, $name = null)
    {

        // try to load method name
        if (isset($row[EntityStatus::MEMBER_NAME])) {
            // try to invoke the method, if available
            if (method_exists($this, $methodName = $row[EntityStatus::MEMBER_NAME])) {
                return $this->$methodName($row, $name);
            }

            // throw an exeption otherwise
            throw new \Exception(sprintf('Can\'t find method name "%s"', $methodName));
        }

        // throw an exeption otherwise
        throw new \Exception(sprintf('Key "%s" with entity status is not available', EntityStatus::MEMBER_NAME));
    }

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The last inserted ID
     */
    public function create(array $row, $name = null)
    {
        return $this->getCreateProcessor()->execute($row, $name, $this->getPrimaryKeyMemberName());
    }

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the updated product
     */
    public function update(array $row, $name = null)
    {
        return $this->getUpdateProcessor()->execute($row, $name, $this->getPrimaryKeyMemberName());
    }
}
