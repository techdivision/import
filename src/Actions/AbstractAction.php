<?php

/**
 * TechDivision\Import\Actions\AbstractAction
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

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Actions\Processors\ProcessorInterface;

/**
 * An abstract action implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Collection\Actions\AbstractAction
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * The primary key name to use.
     *
     * @var string
     */
    protected $primaryKeyMemberName;

    /**
     * The create processor instance.
     *
     * @var \TechDivision\Import\Actions\Processors\ProcessorInterface
     */
    protected $createProcessor;

    /**
     * The delete processor instance.
     *
     * @var \TechDivision\Import\Actions\Processors\ProcessorInterface
     */
    protected $deleteProcessor;

    /**
     * The update processor instance.
     *
     * @var \TechDivision\Import\Actions\Processors\ProcessorInterface
     */
    protected $updateProcessor;

    /**
     * Initialize the instance with the passed processors.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $createProcessor      The create processor instance
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $updateProcessor      The update processor instance
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $deleteProcessor      The delete processor instance
     * @param string|null                                                     $primaryKeyMemberName The primary key member name
     */
    public function __construct(
        ProcessorInterface $createProcessor = null,
        ProcessorInterface $updateProcessor = null,
        ProcessorInterface $deleteProcessor = null,
        $primaryKeyMemberName = null
    ) {

        // query whether or not a create processor has been passed
        if ($createProcessor instanceof ProcessorInterface) {
            $this->setCreateProcessor($createProcessor);
        }

        // query whether or not a update processor has been passed
        if ($updateProcessor instanceof ProcessorInterface) {
            $this->setUpdateProcessor($updateProcessor);
        }

        // query whether or not a delete processor has been passed
        if ($deleteProcessor instanceof ProcessorInterface) {
            $this->setDeleteProcessor($deleteProcessor);
        }

        // initialize the primary key member name
        $this->primaryKeyMemberName = $primaryKeyMemberName;
    }

    /**
     * Return's the primary key member name of the entity to persist.
     *
     * @return string The primary key member name
     */
    public function getPrimaryKeyMemberName()
    {
        return $this->primaryKeyMemberName;
    }

    /**
     * Set's the create processor instance.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface $createProcessor The create processor instance to use
     *
     * @return void
     */
    public function setCreateProcessor(ProcessorInterface $createProcessor)
    {
        $this->createProcessor = $createProcessor;
    }

    /**
     * Return's the create processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The create processor instance
     */
    public function getCreateProcessor()
    {
        return $this->createProcessor;
    }

    /**
     * Set's the delete processor instance.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface $deleteProcessor The delete processor instance to use
     *
     * @return void
     */
    public function setDeleteProcessor(ProcessorInterface $deleteProcessor)
    {
        $this->deleteProcessor = $deleteProcessor;
    }

    /**
     * Return's the delete processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The delete processor instance
     */
    public function getDeleteProcessor()
    {
        return $this->deleteProcessor;
    }

    /**
     * Set's the update processor instance.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface $updateProcessor The update processor instance to use
     *
     * @return void
     */
    public function setUpdateProcessor(ProcessorInterface $updateProcessor)
    {
        $this->updateProcessor = $updateProcessor;
    }

    /**
     * Return's the update processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The update processor instance
     */
    public function getUpdateProcessor()
    {
        return $this->updateProcessor;
    }

    /**
     * Helper method that create/update the passed entity, depending on
     * the entity's status.
     *
     * @param array       $row  The entity data to create/update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persist(array $row, $name = null)
    {

        // load the method name
        $methodName = $row[EntityStatus::MEMBER_NAME];

        // invoke the method
        $this->$methodName($row, $name);
    }

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function create(array $row, $name = null)
    {
        $this->getCreateProcessor()->execute($row, $name, $this->getPrimaryKeyMemberName());
    }

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function delete(array $row, $name = null)
    {
        $this->getDeleteProcessor()->execute($row, $name, $this->getPrimaryKeyMemberName());
    }

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function update(array $row, $name = null)
    {
        $this->getUpdateProcessor()->execute($row, $name, $this->getPrimaryKeyMemberName());
    }
}
