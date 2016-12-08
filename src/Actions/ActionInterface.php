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
     * Return's the processor instance.
     *
     * @return \ITechDivision\Import\Actions\Processors\ProcessorInterface The processor instance
     */
    public function getPersistProcessor();

    /**
     * Persist's the passed row.
     *
     * @param array $row The row to persist
     *
     * @return void
     */
    public function persist($row);

    /**
     * Remove's the entity with the passed attributes.
     *
     * @param array $row The attributes of the entity to remove
     *
     * @return void
     */
    public function remove($row);
}
