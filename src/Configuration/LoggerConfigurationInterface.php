<?php

/**
 * TechDivision\Import\Configuration\LoggerConfigurationInterface
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
 * The interface for a logger configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface LoggerConfigurationInterface extends ParamsConfigurationInterface
{

    /**
     * Return's the logger's channel name to use.
     *
     * @return string The channel name
     */
    public function getChannelName();

    /**
     * Return's the logger's unique name to use.
     *
     * @return string The unique name
     */
    public function getName();

    /**
     * Return's the DI name of the factory used to create the logger instance.
     *
     * @return string The DI name of the factory to use
     */
    public function getId();

    /**
     * Return's the logger's type to use.
     *
     * @return string The type
     */
    public function getType();

    /**
     * Return's the array with the logger's handlers.
     *
     * @return \Doctrine\Common\Collections\Collection The ArrayCollection with the handlers
     */
    public function getHandlers();
}
