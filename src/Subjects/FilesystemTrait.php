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

use League\Flysystem\FilesystemInterface;

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
     * The virtual filesystem instance.
     *
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * The root directory for the virtual filesystem.
     *
     * @var string
     */
    protected $rootDir;

    /**
     * Set's root directory for the virtual filesystem.
     *
     * @param string $rootDir The root directory for the virtual filesystem
     *
     * @return void
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Return's the root directory for the virtual filesystem.
     *
     * @return string The root directory for the virtual filesystem
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * Set's the virtual filesystem instance.
     *
     * @param \League\Flysystem\FilesystemInterface $filesystem The filesystem instance
     *
     * @return void
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Return's the virtual filesystem instance.
     *
     * @return \League\Flysystem\FilesystemInterface The filesystem instance
     */
    public function getFilesystem()
    {
        return $this->filesystem;
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
        if ($this->getFilesystem()->has($path)) {
            return $path;
        }

        // try to prepend the actual working directory, assuming we've a relative path
        if ($this->getFilesystem()->has($path = getcwd() . DIRECTORY_SEPARATOR . $path)) {
            return $path;
        }

        // throw an exception if the passed directory doesn't exists
        throw new \InvalidArgumentException(
            sprintf('Directory %s doesn\'t exist', $path)
        );
    }
}
