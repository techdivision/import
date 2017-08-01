<?php

/**
 * TechDivision\Import\Adapter\LeagueFilesystemAdapterFactory
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

use League\Flysystem\Filesystem;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * A generic filesystem adapter factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LeagueFilesystemAdapterFactory implements FilesystemAdapterFactoryInterface
{

    /**
     * Factory method to create new filesystem adapter instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function createFilesystemAdapter(SubjectConfigurationInterface $subjectConfiguration)
    {

        // the filesystem adapter configuration
        $filesystemAdapterConfiguration = $subjectConfiguration->getFilesystemAdapter();

        // load the filesystem adapter's adapter configuration (FS specific)
        $adapterConfiguration = $filesystemAdapterConfiguration->getAdapter();

        // load the adapter parameters
        $adapterParams = $adapterConfiguration->getParams();

        // initialize the root directory, if not specified in the adapter parameters
        if (!isset($adapterParams[ConfigurationKeys::ROOT])) {
            $adapterParams[ConfigurationKeys::ROOT] = getcwd();
        }

        // load the adapter to use
        $reflectionClass = new \ReflectionClass($adapterConfiguration->getType());
        $adapter =  $reflectionClass->newInstanceArgs(ConfigurationUtil::prepareConstructorArgs($reflectionClass, $adapterParams));

        // create a new filesystem instance
        return new LeagueFilesystemAdapter(new Filesystem($adapter));
    }
}
