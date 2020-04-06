<?php

/**
 * TechDivision\Import\Adapter\PhpFilesystemAdapter
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
 * Adapter for a PHP filesystem implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PhpFilesystemAdapter implements PhpFilesystemAdapterInterface
{

    /**
     * Creates a new directroy.
     *
     * @param string  $pathname The directory path
     * @param integer $mode     The mode is 0700 by default, which means the widest possible access
     *
     * @return boolean TRUE on success, else FALSE
     * @link http://php.net/mkdir
     */
    public function mkdir($pathname, $mode = 0755)
    {
        return mkdir($pathname, $mode, true);
    }

    /**
     * Query whether or not the passed filename exists.
     *
     * @param string $filename The filename to query
     *
     * @return boolean TRUE if the passed filename exists, else FALSE
     * @link http://php.net/is_file
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Tells whether the filename is a directory.
     *
     * @param string $filename Path to the file
     *
     * @return TRUE if the filename exists and is a directory, else FALSE
     * @link http://php.net/is_dir
     */
    public function isDir($filename)
    {
        return is_dir($filename);
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
        return touch($filename);
    }

    /**
     * Renames a file or directory.
     *
     * @param string $oldname The old name
     * @param string $newname The new name
     *
     * @return boolean TRUE on success, else FALSE
     * @link http://php.net/rename
     */
    public function rename($oldname, $newname)
    {
        return rename($oldname, $newname);
    }

    /**
     * Writes the passed data to file with the passed name.
     *
     * @param string $filename The name of the file to write the data to
     * @param string $data     The data to write to the file
     *
     * @return number The number of bytes written to the file
     * @link http://php.net/file_put_contents
     */
    public function write($filename, $data)
    {
        return file_put_contents($filename, $data);
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
        return unlink($filename);
    }

    /**
     * Copy's a file from source to destination.
     *
     * @param string $src  The source file
     * @param string $dest The destination file
     *
     * @return boolean TRUE on success, else FALSE
     * @link http://php.net/copy
     */
    public function copy($src, $dest)
    {
        return copy($src, $dest);
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

        // clear the filecache
        clearstatcache();

        // parse the directory
        $files = $this->glob($pattern = sprintf('%s/*', $directory), 0);

        // parse all subdirectories, if recursive parsing is wanted
        if ($recursive !== false) {
            // load the directories
            $dirs = $this->glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE);
            // iterate over the subdirectories for its files
            foreach ($dirs as $dir) {
                $files = array_merge($files, $this->listContents($dir . DIRECTORY_SEPARATOR . basename($pattern), $recursive));
            }
        }

        // return the array with the files matching the glob pattern
        return $files;
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

        // open the directory
        $dir = opendir($src);

        // remove files/folders recursively
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if ($this->isDir($full)) {
                    $recursive ?? $this->removeDir($full, $recursive);
                } else {
                    if (!$this->delete($full)) {
                        throw new \Exception(sprintf('Can\'t remove file %s', $full));
                    }
                }
            }
        }

        // close handle and remove directory itself
        closedir($dir);
        if (!rmdir($src)) {
            throw new \Exception(sprintf('Can\'t remove directory %s', $src));
        }
    }

    /**
     * Find and return pathnames matching a pattern
     *
     * @param string $pattern No tilde expansion or parameter substitution is done.
     * @param int    $flags   Flags that changes the behaviour
     *
     * @return array Containing the matched files/directories, an empty array if no file matched or FALSE on error
     * @link https://www.php.net/glob
     */
    public function glob(string $pattern, int $flags = 0)
    {

        // clear the filecache
        clearstatcache();

        // invoke the glob and return the array with the found files
        return glob($pattern, $flags);
    }

    /**
     * Return's the size of the file with the passed name.
     *
     * @param string $filename The name of the file to return the size for
     *
     * @return int The size of the file in bytes
     * @throws \Exception  Is thrown, if the size can not be calculated
     * @link https://php.net/filesize
     */
    public function size($filename)
    {

        // calculate the size of the file and return it
        if (is_int($size = filesize($filename))) {
            return $size;
        }

        // throw an exception if the size can not be calculated
        throw new \Exception(sprintf('Can\'t calculate size of file "%s"', $filename));
    }

    /**
     * Read's and return's the content of the file with the passed name as array.
     *
     * @param string $filename The name of the file to return its content for
     *
     * @return array The content of the file as array
     * @throws \Exception  Is thrown, if the file is not accessible
     * @link https://php.net/file
     */
    public function read($filename)
    {

        // read the content of the file and return it
        if ($content = file($filename)) {
            return $content;
        }

        // throw an exception if the content of the file is not accessible
        throw new \Exception(sprintf('Can\'t read the content of file "%s"', $filename));
    }
}
