<?php

/**
 * TechDivision\Import\Actions\IdentifierActionInterface
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

namespace TechDivision\Import\Actions;

/**
 * The interface for all identifier action implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Actions\IdentifierActionInterface
 */
interface IdentifierActionInterface extends ActionInterface
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
    public function persist(array $row, $name = null);

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the created entity
     */
    public function create(array $row, $name = null);

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the updated entity
     */
    public function update(array $row, $name = null);
}
