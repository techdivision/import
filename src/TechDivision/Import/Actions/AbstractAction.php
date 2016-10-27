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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Actions;

use TechDivision\Import\Actions\Processors\ProcessorInterface;

/**
 * An abstract action implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
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
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return void
     */
    public function persist($row)
    {
        $this->getPersistProcessor()->execute($row);
    }
}
