<?php

/**
 * TechDivision\Import\Subjects\FilesystemSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

/**
 * The interface for all subject implementations that supports filesystem handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilesystemSubjectInterface
{

    /**
     * Return's the filesystem adapater instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function getFilesystemAdapter();

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
