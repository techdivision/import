<?php

/**
 * TechDivision\Import\Loaders\FilesystemLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Adapter\FilesystemAdapterInterface;

/**
 * Generic loader implementation that uses a glob compatible pattern
 * to load files from a given directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/glob
 */
class FilesystemLoader implements LoaderInterface
{

    /**
     * The filesystem adapter instance.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    protected $filesystemAdapter;

    /**
     * Construct that initializes the loader with the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     */
    public function __construct(FilesystemAdapterInterface $filesystemAdapter)
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * Loads and returns the files by using the passed glob pattern.
     *
     * If no pattern will be passed to the `load()` method, the files of
     * the actual directory using `getcwd()` will be returned.
     *
     * @param string|null $pattern The pattern to load the files from the filesystem
     *
     * @return array The array with the data
     */
    public function load(string $pattern = null) : array
    {
        return $this->getFilesystemAdapter()->glob($pattern ? $pattern : getcwd() . DIRECTORY_SEPARATOR . '*');
    }

    /**
     * Return's the filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    protected function getFilesystemAdapter() : FilesystemAdapterInterface
    {
        return $this->filesystemAdapter;
    }
}
