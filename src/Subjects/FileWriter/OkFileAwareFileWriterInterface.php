<?php

/**
 * TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileWriter;

use TechDivision\Import\Handlers\OkFileHandlerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Interface for .OK file aware file writer implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface OkFileAwareFileWriterInterface extends FileWriterInterface
{

    /**
     * Set's he .OK file handler instance.
     *
     * @param \TechDivision\Import\Handlers\OkFileHandlerInterface $handler The .OK file handler instance
     *
     * @return void
     */
    public function setHandler(OkFileHandlerInterface $handler) : void;

    /**
     * Set's the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter) : void;

    /**
     * Set's the file resolver configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface $fileResolverConfiguration The file resolver configuration
     *
     * @return void
     */
    public function setFileResolverConfiguration(FileResolverConfigurationInterface $fileResolverConfiguration) : void;

    /**
     * Create's the .OK files for the import with the passed serial.
     *
     * @param string $serial The serial to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $serial) : int;
}
