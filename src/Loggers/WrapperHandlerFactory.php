<?php

/**
 * TechDivision\Import\Loggers\StreamHandlerFactory
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

namespace TechDivision\Import\Loggers;

use TechDivision\Import\Loggers\Handlers\HandlerWrapper;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * Factory implementation that wraps other handler factories.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class WrapperHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The wrapped handler factory.
     *
     * @var \TechDivision\Import\Loggers\HandlerFactoryInterface
     */
    protected $handlerFactory;

    /**
     * Initialize the processor with the handler factory instance that has to be wrapped
     *
     * @param \TechDivision\Import\Loggers\HandlerFactoryInterface $handlerFactory The handler factory we want to wrap
     */
    public function __construct(HandlerFactoryInterface $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * Return's the wrapped handler factory.
     *
     * @return \TechDivision\Import\Loggers\HandlerFactoryInterface The wrapped handler factory
     */
    protected function getHandlerFactory()
    {
        return $this->handlerFactory;
    }

    /**
     * Creates a new formatter instance based on the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration The handler configuration
     *
     * @return \Monolog\Handler\HandlerInterface The handler instance
     */
    public function factory(HandlerConfigurationInterface $handlerConfiguration)
    {

        // create and return the wrapped handlerinstance
        $reflectionClass = new \ReflectionClass(HandlerWrapper::class);
        return $reflectionClass->newInstance($this->getHandlerFactory(), $handlerConfiguration);
    }
}
