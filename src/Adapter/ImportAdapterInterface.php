<?php

/**
 * TechDivision\Import\Adapter\ImportAdapterInterface
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

namespace TechDivision\Import\Adapter;

use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface;

/**
 * Interface for all import adapter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ImportAdapterInterface extends SerializerAwareAdapterInterface
{


    /**
     * Overwrites the default CSV configuration values with the one from the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface $importAdapterConfiguration The configuration to use the values from
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface                     $serializerFactory          The serializer factory instance
     *
     * @return void
     */
    public function init(
        ImportAdapterConfigurationInterface $importAdapterConfiguration,
        SerializerFactoryInterface $serializerFactory
    );

    /**
     * Imports the content of the CSV file with the passed filename.
     *
     * @param callable $callback The callback that processes the row
     * @param string   $filename The filename to process
     *
     * @return void
     */
    public function import(callable $callback, $filename);
}
