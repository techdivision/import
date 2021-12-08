<?php

/**
 * TechDivision\Import\Handlers\HandlerFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

/**
 * Interface for handler factory implementations
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface HandlerFactoryInterface
{

    /**
     * Create's and return's a new handler instance.
     *
     * @return \TechDivision\Import\Handlers\HandlerInterface The new handler instance
     */
    public function createHandler() : HandlerInterface;
}
