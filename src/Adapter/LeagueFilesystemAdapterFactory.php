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

use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\ConfigurationUtil;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * A generic filesystem adapter factory implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since version 16.8.9 use \TechDivision\Import\Adapter\PhpFilesystemAdapterFactory instead
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
        throw new Exception('LeagueFilesystemAdapterFactory is depracated cause vulnerable version. Please use PhpFilesystemAdapterFactory.');
    }
}
