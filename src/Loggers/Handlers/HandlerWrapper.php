<?php

/**
 * TechDivision\Import\Loggers\Handlers\HandlerWrapper
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
use Monolog\Formatter\FormatterInterface;
use TechDivision\Import\Loggers\HandlerFactoryInterface;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * A wrapper implementation for log handlers that adds a reset() method that allows to reset
 * log handlers e. g. in case the target directory changes during the import process.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class HandlerWrapper implements ResetAwareHandlerInterface
{

    /**
     * The wrapped handler instance.
     *
     * @var \Monolog\Handler\HandlerInterface
     */
    protected $handler;

    /**
     * The handler factory instance.
     *
     * @var \TechDivision\Import\Loggers\HandlerFactoryInterface
     */
    protected $handlerFactory;

    /**
     * The handler configuration instance.
     *
     * @var \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface
     */
    protected $handlerConfiguration;

    /**
     * Initializes the wrapper with the handler instance.
     *
     * @param \TechDivision\Import\Loggers\HandlerFactoryInterface                    $handlerFactory       The handler factory instance
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration instance
     */
    public function __construct(HandlerFactoryInterface $handlerFactory, HandlerConfigurationInterface $handlerConfiguration)
    {

        // initialize the handler factory and the configuration
        $this->handlerFactory = $handlerFactory;
        $this->handlerConfiguration = $handlerConfiguration;

        // initialze the wrapper
        $this->reset();
    }

    /**
     * Reset's the handler instance.
     *
     * @return void
     */
    public function reset()
    {

        // initialize the variable for the old formatter instance
        $oldFormatter = null;

        // try to preserve the old formatter instance
        if ($this->handler instanceof HandlerInterface) {
            $oldFormatter = $this->handler->getFormatter();
        }

        // create a new handler instance
        $this->handler = $this->handlerFactory->factory($this->handlerConfiguration);

        // set the old formatter instance, if available
        if ($oldFormatter instanceof FormatterInterface) {
            $this->handler->setFormatter($oldFormatter);
        }
    }

    /**
     * Checks whether the given record will be handled by this handler.
     *
     * This is mostly done for performance reasons, to avoid calling processors for nothing.
     *
     * Handlers should still check the record levels within handle(), returning false in isHandling()
     * is no guarantee that handle() will not be called, and isHandling() might not be called
     * for a given record.
     *
     * @param array $record Partial log record containing only a level key
     *
     * @return boolean TRUE if the handler has to handle the record, FALSE otherwise
     */
    public function isHandling(array $record): bool
    {
        return $this->handler->isHandling($record);
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     *
     * @return boolean TRUE means that this handler handled the record, and that bubbling is not permitted.
     *                 FALSE means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(array $record): bool
    {
        return $this->handler->handle($record);
    }

    /**
     * Handles a set of records at once.
     *
     * @param array $records The records to handle (an array of record arrays)
     *
     * @return void
     */
    public function handleBatch(array $records): void
    {
        $this->handler->handleBatch($records);
    }

    /**
     * Return's the handlers formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface The formatter instance
     */
    public function getFormatter()
    {
        return $this->handler->getFormatter();
    }

    /**
     * Adds a processor in the stack.
     *
     * @param callable $callback The processor to add
     *
     * @return \TechDivision\Import\Loggers\Handlers\HandlerWrapper The handler instance
     */
    public function pushProcessor($callback)
    {
        $this->handler->pushProcessor($callback);
        return $this;
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return \TechDivision\Import\Loggers\Handlers\HandlerWrapper The handler instance
     */
    public function popProcessor()
    {
        $this->handler->popProcessor();
        return $this;
    }

    /**
     * Sets the handler's formatter instance.
     *
     * @param  \Monolog\Formatter\FormatterInterface $formatter The formatter instance
     *
     * @return \TechDivision\Import\Loggers\Handlers\HandlerWrapper The handler instance
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->handler->setFormatter($formatter);
        return $this;
    }

    /**
     *  Closes the handler.
     *
     * @return void
     */
    public function close(): void
    {
        // Nothing to do
    }
}
