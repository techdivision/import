<?php

/**
 * TechDivision\Import\Handlers\OkFileHandlerInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

use TechDivision\Import\Loaders\FilteredLoaderInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;

/**
 * Interface for all .OK file handler implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface OkFileHandlerInterface extends HandlerInterface
{

    /**
     * Set's the loader instance.
     *
     * @param \TechDivision\Import\Loaders\FilteredLoaderInterface $loader The loader instance
     *
     * @return void
     */
    public function setLoader(FilteredLoaderInterface $loader) : void;

    /**
     * Set's the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter) : void;

    /**
     * Query's whether or not the passed file is available and can be used as .OK file.
     *
     * @param string $okFilename The .OK file that has to be queried
     *
     * @return bool TRUE if the passed filename is an .OK file, else FALSE
     */
    public function isOkFile(string $okFilename) : bool;

    /**
     * Query whether or not, the passed CSV filename is in the passed OK file. If the filename was found,
     * the OK file will be cleaned-up.
     *
     * @param string $filename   The filename to be cleaned-up
     * @param string $okFilename The filename of the .OK filename
     *
     * @return bool TRUE if the passed filename matches the also passed .OK file
     * @throws \Exception Is thrown, if the passed filename is NOT in the OK file or the OK can not be cleaned-up
     */
    public function cleanUpOkFile(string $filename, string $okFilename) : bool;

    /**
     * Create's the .OK files for all .CSV files that matches the passed pattern.
     *
     * @param string $pattern The pattern that matches the .CSV files we want to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $pattern) : int;
}
