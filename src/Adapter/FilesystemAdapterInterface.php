<?php

/**
 * TechDivision\Import\Adapter\FilesystemAdapterInterface
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
 * Interface for the filesystem adapters.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FilesystemAdapterInterface
{

    /**
     * Creates a new directroy.
     *
     * @param string  $pathname The directory path
     * @param integer $mode     The mode is 0700 by default, which means the widest possible access
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function mkdir($pathname, $mode = 0700);

    /**
     * Query whether or not the passed filename exists.
     *
     * @param string $filename The filename to query
     *
     * @return boolean TRUE if the passed filename exists, else FALSE
     */
    public function isFile($filename);

    /**
     * Tells whether the filename is a directory.
     *
     * @param string $filename Path to the file
     *
     * @return TRUE if the filename exists and is a directory, else FALSE
     */
    public function isDir($filename);

    /**
     * Creates an empty file with the passed filename.
     *
     * @param string $filename The name of the file to create
     *
     * @return boolean TRUE if the file can be created, else FALSE
     */
    public function touch($filename);

    /**
     * Renames a file or directory.
     *
     * @param string $oldname The old name
     * @param string $newname The new name
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function rename($oldname, $newname);
    /**
     * Writes the passed data to file with the passed name.
     *
     * @param string $filename The name of the file to write the data to
     * @param string $data     The data to write to the file
     *
     * @return number The number of bytes written to the file
     */
    public function write($filename, $data);

    /**
     * Copy's a file from source to destination.
     *
     * @param string $src  The source file
     * @param string $dest The destination file
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function copy($src, $dest);

    /**
     * List the filenames of a directory.
     *
     * @param string  $directory The directory to list
     * @param boolean $recursive Whether to list recursively
     *
     * @return array A list of filenames
     */
    public function listContents($directory = '', $recursive = false);
}
