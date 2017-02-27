<?php

/**
 * TechDivision\Import\Subjects\FilesystemSubjectInterface
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

namespace TechDivision\Import\Subjects;

/**
 * The interface for all subject implementations that supports filesystem handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilesystemSubjectInterface
{

    /**
     * Return's the root directory for the virtual filesystem.
     *
     * @return string The root directory for the virtual filesystem
     */
    public function getRootDir();

    /**
     * Return's the virtual filesystem instance.
     *
     * @return \League\Flysystem\FilesystemInterface The filesystem instance
     */
    public function getFilesystem();

    /**
     * This method tries to resolve the passed path and returns it. If the path
     * is relative, the actual working directory will be prepended.
     *
     * @param string $path The path to be resolved
     *
     * @return string The resolved path
     * @throws \InvalidArgumentException Is thrown, if the path can not be resolved
     */
    public function resolvePath($path);
}
