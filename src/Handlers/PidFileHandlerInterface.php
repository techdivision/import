<?php

/**
 * TechDivision\Import\Handlers\PidFileHandler
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
 * A generic interface for PID file handler implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PidFileHandlerInterface extends HandlerInterface
{

    /**
     * Persist the UUID of the actual import process to the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be locked or the PID can not be added
     * @throws \TechDivision\Import\Exceptions\ImportAlreadyRunningException Is thrown, if a import process is already running
     */
    public function lock();

    /**
     * Remove's the UUID of the actual import process from the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be removed
     */
    public function unlock();
}
