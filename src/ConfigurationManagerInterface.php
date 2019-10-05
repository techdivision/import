<?php

/**
 * TechDivision\Import\ConfigurationManagerInterface
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

namespace TechDivision\Import;

/**
 * The interface for the import manager implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ConfigurationManagerInterface
{

    /**
     * Return's the managed configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration();

    /**
     * Return's an array with the configurations of the operations that has to be executed.
     *
     * @return \TechDivision\Import\Configuration\OperationConfigurationInterface[] The operation configurations
     */
    public function getOperations();

    /**
     * Return's the array with the plugins that has to be executed.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the plugins
     * @throws \Exception Is thrown, if no plugins are available for the actual operation
     */
    public function getPlugins();
}
