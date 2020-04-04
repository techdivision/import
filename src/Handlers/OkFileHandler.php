<?php

/**
 * TechDivision\Import\Handlers\PidFileHandler
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
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Exceptions\OkFileNotEmptyException;

/**
 * An .OK file handler implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileHandler implements OkFileHandlerInterface
{

    /**
     * The loader instance used to load the proposed .OK filenames and it's content.
     *
     * @var \TechDivision\Import\Loaders\FilteredLoaderInterface
     */
    private $loader;

    /**
     * The filesystem adapter instance.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    private $filesystemAdapter;

    /**
     * The generic file handler instance.
     *
     * @var \TechDivision\Import\Handlers\GenericFileHandlerInterface
     */
    private $genericFileHandler;

    /**
     * Initializes the file handler instance.
     *
     * @param \TechDivision\Import\Handlers\GenericFileHandlerInterface|null $genericFileHandler The generic file handler instance
     */
    public function __construct(GenericFileHandlerInterface $genericFileHandler = null)
    {
        $this->genericFileHandler = $genericFileHandler ?? new GenericFileHandler();
    }

    /**
     * Return's the generic file handler instance.
     *
     * @return \TechDivision\Import\Handlers\GenericFileHandlerInterface The generic file handler instance
     */
    protected function getGenericFileHandler()
    {
        return $this->genericFileHandler;
    }

    /**
     * Return's the filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    protected function getFilesystemAdapter()
    {
        return $this->filesystemAdapter;
    }

    /**
     * Return's the loader instance used to load the proposed .OK filenames and it's content.
     *
     * @return \TechDivision\Import\Loaders\FilteredLoaderInterface The loader instance
     */
    protected function getLoader() : FilteredLoaderInterface
    {
        return $this->loader;
    }

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string   $line The line to be removed
     * @param resource $fh   The file handle of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    protected function removeLineFromFile(string $line, $fh) : void
    {
        $this->getGenericFileHandler()->removeLineFromFile($line, $fh);
    }

    /**
     * Set's the loader instance used to load the proposed .OK filenames and it's content.
     *
     * @param \TechDivision\Import\Loaders\FilteredLoaderInterface $loader The loader instance
     *
     * @return void
     */
    public function setLoader(FilteredLoaderInterface $loader) : void
    {
        $this->loader = $loader;
    }

    /**
     * Set's the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter The filesystem adapter instance
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter) : void
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * Deletes the .OK file with the passed name, but only if it is empty.
     *
     * @param string $okFilename The name of the .OK file to delete
     *
     * @return void
     * @throw \TechDivision\Import\Exceptions\OkFileNotEmptyException Is thrown, if the .OK file is NOT empty
     */
    public function delete(string $okFilename)
    {

        // if the .OK file is empty, delete it
        if (filesize($okFilename) === 0) {
            $this->getFilesystemAdapter()->delete($okFilename);
        } else {
            throw new OkFileNotEmptyException(sprintf('Can\'t delete file "%s" because it\'s not empty', $okFilename));
        }
    }

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
    public function cleanUpOkFile(string $filename, string $okFilename) : void
    {

        try {
            // else, remove the CSV filename from the OK file
            $this->removeLineFromFile(basename($filename), $fh = fopen($okFilename, 'r+'));
            fclose($fh);

            // finally delete the .OK file, if empty
            if (filesize($okFilename) === 0) {
                $this->delete($okFilename);
            }

        } catch (LineNotFoundException $lne) {
            // wrap and re-throw the exception
            throw new \Exception(
                sprintf(
                    'Can\'t remove filename %s from OK file: %s',
                    $filename,
                    $okFilename
                ),
                null,
                $lne
            );
        }
    }

    /**
     * Create's the .OK files for all .CSV files that matches the passed pattern.
     *
     * @param string $pattern The pattern that matches the .CSV files we want to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $pattern) : int
    {

        // initialize the counter for the processed .OK files
        $counter = 0;

        // load the array with the proposed .OK filenames
        $proposedOkFilenames = $this->getLoader()->load($pattern);

        // create the proposed .OK files
        foreach ($proposedOkFilenames as $okFilename => $csvFilenames) {
            // write the proposed .OK file
            if ($this->getFilesystemAdapter()->write($okFilename, implode(PHP_EOL, $csvFilenames)) === false) {
                throw new \Exception(sprintf('Can\' create .OK file "%s"', $okFilename));
            }
            // raise the counter
            $counter++;
        }

        // return the number of created .OK files
        return $counter;
    }
}
