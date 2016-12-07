<?php

/**
 * TechDivision\Import\Services\RegistryProcessorFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Services;

use TechDivision\Import\ConfigurationInterface;

/**
 * Factory to create a new registry processor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class RegistryProcessorFactory
{

    /**
     * Factory method to create a new import processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return object The processor instance
     */
    public static function factory(\PDO $connection, ConfigurationInterface $configuration)
    {
        return new RegistryProcessor();
    }
}
