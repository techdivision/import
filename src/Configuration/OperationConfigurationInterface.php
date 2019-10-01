<?php

/**
 * TechDivision\Import\Configuration\OperationConfigurationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * Interface for the operation configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface OperationConfigurationInterface
{

    /**
     * Query's whether or not the passed operation equals this instance.
     *
     * @param \TechDivision\Import\Configuration\OperationConfigurationInterface $operation The operation to query
     *
     * @return boolean TRUE if the operations are equal, else FALSE
     */
    public function equals(OperationConfigurationInterface $operation);

    /**
     * Return's the operation's name.
     *
     * @return string The operation's class name
     */
    public function getName();

    /**
     * Return's the array with the configured listeners.
     *
     * @return array The array with the listeners
     */
    public function getListeners();

    /**
     * Return's the ArrayCollection with the operation's plugins.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operation's plugins
     */
    public function getPlugins();

    /**
     * String representation of the operation (the name).
     *
     * @return string The operation name
     */
    public function __toString();

    /**
     * Set's the execution context configuration for the actualy plugin configuration.
     *
     * @param \TechDivision\Import\Configuration\ExecutionContextConfigurationInterface $executionContext The execution context to use
     *
     * @return void
     */
    public function setExecutionContext(ExecutionContextConfigurationInterface $executionContext);

    /**
     * Return's the execution context configuration for the actualy plugin configuration.
     *
     * @return \TechDivision\Import\Configuration\ExecutionContextConfigurationInterface The execution context to use
     */
    public function getExecutionContext();
}
