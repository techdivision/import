<?php

/**
 * TechDivision\Import\Subjects\FilesystemTrait
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

use TechDivision\Import\Adapter\FilesystemAdapterInterface;

/**
 * The trait implementation that provides filesystem handling functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait FilesystemTrait
{

    /**
     * The filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    protected $filesystemAdapater;

    /**
     * Set's the virtual filesystem instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter)
    {
        $this->filesystemAdapater = $filesystemAdapter;
    }

    /**
     * Return's the filesystem adapater instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function getFilesystemAdapter()
    {
        return $this->filesystemAdapater;
    }

    /**
     * This method tries to resolve the passed path and returns it. If the path
     * is relative, the actual working directory will be prepended.
     *
     * @param string $path The path to be resolved
     *
     * @return string The resolved path
     * @throws \InvalidArgumentException Is thrown, if the path can not be resolved
     */
    public function resolvePath($path)
    {

        // if we've an absolute path, return it immediately
        if ($this->getFilesystemAdapter()->isDir($path)) {
            return $path;
        }

        // temporarily save the path
        $originalPath = $path;

        // try to prepend the actual working directory, assuming we've a relative path
        if ($this->getFilesystemAdapter()->isDir($path = getcwd() . DIRECTORY_SEPARATOR . ltrim($path, '/'))) {
            return $path;
        }

        // throw an exception if the passed directory doesn't exists
        throw new \InvalidArgumentException(
            sprintf('Directory %s doesn\'t exist', $originalPath)
        );
    }

    /**
     * Creates a new directroy.
     *
     * @param string  $pathname  The directory path
     * @param integer $mode      The mode is 0777 by default, which means the widest possible access
     * @param string  $recursive Allows the creation of nested directories specified in the pathname
     *
     * @return boolean TRUE on success, else FALSE
     * @link http://php.net/mkdir
     */
    public function mkdir($pathname, $mode = 0700, $recursive = false)
    {
        return $this->getFilesystemAdapter()->mkdir($pathname, $mode, $recursive);
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
        return $this->getFilesystemAdapter()->isFile($filename);
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
        return $this->getFilesystemAdapter()->isDir($filename);
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
        return $this->getFilesystemAdapter()->touch($filename);
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
        return $this->getFilesystemAdapter()->rename($oldname, $newname);
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
        return $this->getFilesystemAdapter()->write($filename, $data);
    }
}
