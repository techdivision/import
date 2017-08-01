<?php

/**
 * TechDivision\Import\Configuration\Subject\FilesystemAdapterConfigurationInterface
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

namespace TechDivision\Import\Configuration\Subject;

/**
 * The interface for a filesystem adapter's configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilesystemAdapterConfigurationInterface
{

    /**
     * Return's the filesystem adapter's unique DI identifier.
     *
     * @return string The filesystem adapter's unique DI identifier
     */
    public function getId();

    /**
     * Return's the filesystem specific adapter configuration.
     *
     * @return \TechDivision\Import\Configuration\Subject\FilesystemAdapter\AdapterConfigurationInterface The filesystem specific adapter configuration
     */
    public function getAdapter();
}
