<?php

/**
 * TechDivision\Import\Adapter\PhpFilesystemAdapterInterface
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

/**
 * Interface for the PHP filesystem adapter.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PhpFilesystemAdapterInterface extends FilesystemAdapterInterface
{

    /**
     * Removes the passed directory recursively.
     *
     * @param string  $src       Name of the directory to remove
     * @param boolean $recursive TRUE if the directory has to be deleted recursive, else FALSE
     *
     * @return void
     * @throws \Exception Is thrown, if the directory can not be removed
     */
    public function removeDir($src, $recursive = false);

    /**
     * Find and return pathnames matching a pattern
     *
     * @param string  $pattern No tilde expansion or parameter substitution is done.
     * @param int     $flags   Flags that changes the behaviour
     *
     * @return array Containing the matched files/directories, an empty array if no file matched or FALSE on error
     */
    public function glob(string $pattern, int $flags = 0);
}
