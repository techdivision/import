<?php

/**
 * TechDivision\Import\Handlers\OkFileHandlerInterface
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

use TechDivision\Import\Loaders\FilteredLoaderInterface;

/**
 * Interface for all .OK file handler implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * Deletes the .OK file with the passed name, but only if it is empty.
     *
     * @param string $okFilename The name of the .OK file to delete
     *
     * @return void
     * @throw \TechDivision\Import\Exceptions\OkFileNotEmptyException Is thrown, if the .OK file is NOT empty
     */
    public function delete(string $okFilename);

    /**
     * Query whether or not, the passed CSV filename is in the passed OK file. If the filename was found,
     * the OK file will be cleaned-up.
     *
     * @param string $filename   The filename to be cleaned-up
     * @param string $okFilename The filename of the .OK filename
     *
     * @return void
     * @throws \Exception Is thrown, if the passed filename is NOT in the OK file or the OK can not be cleaned-up
     */
    public function cleanUpOkFile(string $filename, string $okFilename) : void;

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
