<?php

/**
 * TechDivision\Import\ConfigurationFactoryInterface
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

namespace TechDivision\Import;

/**
 * The interface for the configuration factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ConfigurationFactoryInterface
{

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * @param string $filename The configuration filename
     * @param string $type     The format of the configuration file, either one of json, yaml or xml
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     * @throws \Exception Is thrown, if the specified configuration file doesn't exist
     */
    public function factory($filename, $type = 'json');
}
