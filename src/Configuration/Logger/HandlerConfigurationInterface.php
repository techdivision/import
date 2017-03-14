<?php

/**
 * TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface
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

namespace TechDivision\Import\Configuration\Logger;

/**
 * The interface for a logger's handler configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface HandlerConfigurationInterface
{

    /**
     * Return's the handler's type to use.
     *
     * @return string The type
     */
    public function getType();

    /**
     * Return's the handler's formtter to use.
     *
     * @return \TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface
     */
    public function getFormatter();

    /**
     * Return's the array with the params.
     *
     * @return array The params
     */
    public function getParams();

    /**
     * Query whether or not the param with the passed name exists.
     *
     * @param string $name The name of the param to be queried
     *
     * @return boolean TRUE if the requested param exists, else FALSE
     */
    public function hasParam($name);

    /**
     * Return's the param with the passed name.
     *
     * @param string $name         The name of the param to return
     * @param mixed  $defaultValue The default value if the param doesn't exists
     *
     * @return string The requested param
     * @throws \Exception Is thrown, if the requested param is not available
     */
    public function getParam($name, $defaultValue = null);
}
