<?php

/**
 * TechDivision\Import\Loggers\Handlers\ResetAwareHandlerInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loggers\Handlers;

use Monolog\Handler\HandlerInterface;

/**
 * Interface for reset() method aware handler implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ResetAwareHandlerInterface extends HandlerInterface
{

    /**
     * Reset's the handler instance.
     *
     * @return void
     */
    public function reset();
}
