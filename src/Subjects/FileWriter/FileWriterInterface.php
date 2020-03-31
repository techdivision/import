<?php

/**
 * TechDivision\Import\Subjects\FileWriter\FileWriterInterface
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

namespace TechDivision\Import\Subjects\FileWriter;

use TechDivision\Import\Subjects\FileResolver\FileResolverInterface;

/**
 * Interface for all file writer implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileWriterInterface
{

    /**
     * Set's the file resolver intance.
     *
     * @param \TechDivision\Import\Subjects\FileResolver\FileResolverInterface $fileResolver The file resolver instance
     *
     * @return void
     */
    public function setFileResolver(FileResolverInterface $fileResolver);

    /**
     * Return's the file resolver instance.
     *
     * @return \TechDivision\Import\Subjects\FileResolver\FileResolverInterface The file resolver instance
     */
    public function getFileResolver() : FileResolverInterface;

    /**
     * Create's the .OK files for the import with the passed serial.
     *
     * @param string $serial The serial to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $serial) : int;

    /**
     * Return's an array with the proposed .OK filenames as key and the matching CSV
     * files as values for the import with the passed serial.
     *
     * Based on the passed serial, the files with the configured prefix from the also
     * configured source directory will be loaded and processed to return the array
     * with the proposed .OK filenames.
     *
     * @param string $serial The serial to load the array with the proposed .OK files for
     *
     * @return array The array with key => value pairs of the proposed .OK and the matching CSV files
     */
    public function propsedOkFilenames(string $serial) : array;

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     */
    public function loadFiles(string $serial) : array;
}
