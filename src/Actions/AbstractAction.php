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

use TechDivision\Import\Actions\Processors\ProcessorInterface;

/**
 * An abstract action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * The persist processor instance.
     *
     * @var \TechDivision\Import\Actions\Processors\ProcessorInterface
     */
    protected $persistProcessor;

    /**
     * The remove processor instance.
     *
     * @var \TechDivision\Import\Actions\Processors\ProcessorInterface
     */
    protected $removeProcessor;

    /**
     * Set's the persist processor instance.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface $persistProcessor The persist processor instance to use
     *
     * @return void
     */
    public function setPersistProcessor(ProcessorInterface $persistProcessor)
    {
        $this->persistProcessor = $persistProcessor;
    }

    /**
     * Return's the processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The processor instance
     */
    public function getPersistProcessor()
    {
        return $this->persistProcessor;
    }

    /**
     * Set's the remove processor instance.
     *
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface $removeProcessor The remove processor instance to use
     *
     * @return void
     */
    public function setRemoveProcessor(ProcessorInterface $removeProcessor)
    {
        $this->removeProcessor = $removeProcessor;
    }

    /**
     * Return's the processor instance.
     *
     * @return \TechDivision\Import\Actions\Processors\ProcessorInterface The processor instance
     */
    public function getRemoveProcessor()
    {
        return $this->removeProcessor;
    }

    /**
     * Persist's the passed row.
     *
     * @param array       $row  The row to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persist($row, $name = null)
    {
        $this->getPersistProcessor()->execute($row, $name);
    }

    /**
     * Remove's the entity with the passed attributes.
     *
     * @param array  $row       The attributes of the entity to remove
     * @param string $name|null The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function remove($row, $name = null)
    {
        $this->getRemoveProcessor()->execute($row, $name);
    }
}
