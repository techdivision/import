<?php

/**
 * TechDivision\Import\Handlers\PidFileHandler
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
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Exceptions\OkFileNotEmptyException;

/**
 * An .OK file handler implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed from
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\LineNotFoundException Is thrown, if the requested line can not be found in the file
     * @throws \Exception Is thrown, if the file can not be written, after the line has been removed
     * @see \TechDivision\Import\Handlers\GenericFileHandlerInterface::removeLineFromFile()
     */
    protected function removeLineFromFilename(string $line, string $filename) : void
    {
        $this->getGenericFileHandler()->removeLineFromFilename($line, $filename);
    }

    /**
     * Strips the passed suffix, including the (.), from the filename and returns it.
     *
     * @param string $filename The filename to return the suffix from
     *
     * @return string The filname without the suffix
     */
    protected function stripSuffix(string $filename) : string
    {
        return basename($filename, sprintf('.%s', pathinfo($filename, PATHINFO_EXTENSION)));
    }

    /**
     * Return's the size of the file with the passed name.
     *
     * @param string $okFilename The name of the file to return the size for
     *
     * @return int The size of the file in bytes
     * @throws \Exception Is thrown, if the size can not be calculated
     */
    protected function size($okFilename)
    {
        return $this->getFilesystemAdapter()->size($okFilename);
    }

    /**
     * Deletes the .OK file with the passed name, but only if it is empty.
     *
     * @param string $okFilename The name of the .OK file to delete
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\OkFileNotEmptyException Is thrown, if the .OK file is NOT empty
     */
    protected function delete(string $okFilename)
    {

        // if the .OK file is empty, delete it
        if (filesize($okFilename) === 0) {
            $this->getFilesystemAdapter()->delete($okFilename);
        } else {
            throw new OkFileNotEmptyException(sprintf('Can\'t delete file "%s" because it\'s not empty', $okFilename));
        }
    }

    /**
     * Query whether or not the basename, without suffix, of the passed filenames are equal.
     *
     * @param string $filename   The filename to compare
     * @param string $okFilename The name of the .OK file to compare
     *
     * @return boolean TRUE if the passed files basename are equal, else FALSE
     */
    protected function isEqualFilename(string $filename, string $okFilename) : bool
    {

        // strip the suffix of the files and compare them
        if (strcmp($this->stripSuffix($filename), $this->stripSuffix($okFilename)) === 0) {
            // return TRUE, if the filenames ARE equal
            return true;
        }

        // return FALSE, if the filenames are NOT equal
        return false;
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
     *
     * @return void
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter) : void
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * Query's whether or not the passed file is available and can be used as .OK file.
     *
     * @param string $okFilename The .OK file that has to be queried
     *
     * @return bool TRUE if the passed filename is an .OK file, else FALSE
     */
    public function isOkFile(string $okFilename) : bool
    {
        return $this->getFilesystemAdapter()->isFile($okFilename);
    }

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
    public function cleanUpOkFile(string $filename, string $okFilename) : bool
    {

        try {
            // if the OK filename matches the CSV filename AND the OK file is empty
            if ($this->isEqualFilename($filename, $okFilename) && $this->size($okFilename) === 0) {
                // finally delete the .OK file
                $this->delete($okFilename);
                // and return TRUE
                return true;
            } else {
                // if the OK filename matches the CSV filename AND the OK file is empty
                foreach ($this->getFilesystemAdapter()->read($okFilename) as $line) {
                    if (strcmp(basename($filename), $trimmedLine = trim($line, PHP_EOL)) === 0) {
                        // else, remove the CSV filename from the OK file
                        $this->removeLineFromFilename($trimmedLine, $okFilename);
                        // finally delete the .OK file, if empty
                        if ($this->size($okFilename) === 0) {
                            $this->delete($okFilename);
                        }
                        // return TRUE, when the line has successfully been removed
                        return true;
                    }
                }
            }
        } catch (LineNotFoundException $lne) {
            // wrap and re-throw the exception
            throw new \Exception(
                sprintf(
                    'Can\'t remove filename "%s" from .OK file: "%s"',
                    $filename,
                    $okFilename
                ),
                0,
                $lne
            );
        }
        // otherwise, return FALSE
        return false;
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
