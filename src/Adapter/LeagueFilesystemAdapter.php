<?php

/**
 * TechDivision\Import\Adapter\LeagueFilesystemAdapter
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
 * Adapter for the league filesystem implementation.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2016 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since version 16.8.9 use \TechDivision\Import\Adapter\PhpFilesystemAdapter instead
 */
class LeagueFilesystemAdapter implements FilesystemAdapterInterface
{
    /**
     * Creates a new directroy.
     *
     * @param string  $pathname The directory path
     * @param integer $mode     The mode is 0700 by default, which means the widest possible access
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function mkdir($pathname, $mode = 0755)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Query whether or not the passed filename exists.
     *
     * @param string $filename The filename to query
     *
     * @return boolean TRUE if the passed filename exists, else FALSE
     */
    public function isFile($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Tells whether the filename is a directory.
     *
     * @param string $filename Path to the file
     *
     * @return TRUE if the filename exists and is a directory, else FALSE
     */
    public function isDir($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Creates an empty file with the passed filename.
     *
     * @param string $filename The name of the file to create
     *
     * @return boolean TRUE if the file can be created, else FALSE
     */
    public function touch($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Renames a file or directory.
     *
     * @param string $oldname The old name
     * @param string $newname The new name
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function rename($oldname, $newname)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Writes the passed data to file with the passed name.
     *
     * @param string $filename The name of the file to write the data to
     * @param string $data     The data to write to the file
     *
     * @return number The number of bytes written to the file
     */
    public function write($filename, $data)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Delete the file with the passed name.
     *
     * @param string $filename The name of the file to be deleted
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function delete($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Copy's a file from source to destination.
     *
     * @param string $src  The source file
     * @param string $dest The destination file
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function copy($src, $dest)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * List the filenames of a directory.
     *
     * @param string  $directory The directory to list
     * @param boolean $recursive Whether to list recursively
     *
     * @return array A list of filenames
     */
    public function listContents($directory = '', $recursive = false)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Removes the passed directory recursively.
     *
     * @param string  $src       Name of the directory to remove
     * @param boolean $recursive TRUE if the directory has to be deleted recursive, else FALSE
     *
     * @return void
     * @throws \Exception Is thrown, if the directory can not be removed
     */
    public function removeDir($src, $recursive = false)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Find and return pathnames matching a pattern
     *
     * @param string $pattern No tilde expansion or parameter substitution is done.
     * @param int    $flags   Flags that changes the behaviour
     *
     * @return array Containing the matched files/directories, an empty array if no file matched or FALSE on error
     */
    public function glob(string $pattern, int $flags = 0)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Return's the size of the file with the passed name.
     *
     * @param string $filename The name of the file to return the size for
     *
     * @return int The size of the file in bytes
     * @throws \Exception  Is thrown, if the size can not be calculated
     */
    public function size($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }

    /**
     * Read's and return's the content of the file with the passed name as array.
     *
     * @param string $filename The name of the file to return its content for
     *
     * @return array The content of the file as array
     * @throws \Exception  Is thrown, if the file is not accessible
     */
    public function read($filename)
    {
        throw new Exception('LeagueFilesystemAdapter is depracated cause vulnerable version. Please use PhpFilesystemAdapter.');
    }
}
