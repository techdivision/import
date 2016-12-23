<?php

/**
 * TechDivision\Import\Actions\ActionInterface
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
 * The interface for all action implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ActionInterface
{

    /**
     * Return's the create processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The create processor instance
     */
    public function getCreateProcessor();

    /**
     * Return's the delete processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The delete processor instance
     */
    public function getDeleteProcessor();

    /**
     * Return's the update processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The update processor instance
     */
    public function getUpdateProcessor();

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function create($row, $name = null);

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function delete($row, $name = null);

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function update($row, $name = null);
}
