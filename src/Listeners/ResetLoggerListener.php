<?php

/**
 * TechDivision\Import\Listeners\ResetLoggerListener
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\Loggers\Handlers\ResetAwareHandlerInterface;

/**
 * Listener implementation that reset the system loggers.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ResetLoggerListener extends \League\Event\AbstractListener
{

    /**
     * The collection with the system loggers.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $systemLoggers;

    /**
     * Initializes the listener with the system logger instances.
     *
     * @param \Doctrine\Common\Collections\Collection $systemLoggers The collection with the system loggers instances
     */
    public function __construct(Collection $systemLoggers)
    {
        $this->systemLoggers = $systemLoggers;
    }

    /**
     * Return's the collection with the system loggers.
     *
     * @return \Doctrine\Common\Collections\Collection The system loggers
     */
    protected function getSystemLoggers()
    {
        return $this->systemLoggers;
    }

    /**
     * Handle the event.
     *
     * Deletes the tier prices for all the products, which have been touched by the import,
     * and which were not part of the tier price import.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // iterate over the system loggers and reset the handlers
        foreach ($this->getSystemLoggers() as $systemLogger) {
            foreach ($systemLogger->getHandlers() as $handler) {
                if ($handler instanceof ResetAwareHandlerInterface) {
                    $handler->reset();
                }
            }
        }
    }
}
