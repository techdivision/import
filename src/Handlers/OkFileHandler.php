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
use TechDivision\Import\Exceptions\MissingOkFileException;
use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

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
     * The generic file handler instance.
     *
     * @var \TechDivision\Import\Handlers\GenericFileHandlerInterface
     */
    private $genericFileHandler;

    /**
     * The filesystem adapter instance.
     *
     * @var \TechDivision\Import\Adapter\FilesystemAdapterInterface
     */
    private $filesystemAdapter;

    /**
     * The file resolver configuration instance.
     *
     * @var \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface
     */
    private $fileResolverConfiguration;

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
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    protected function getFileResolverConfiguration() : FileResolverConfigurationInterface
    {
        return $this->fileResolverConfiguration;
    }

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    protected function removeLineFromFile(string $line, string $filename) : void
    {
        $this->getGenericFileHandler()->removeLineFromFile($line, $filename);
    }

    /**
     * Query whether or not the basename, without suffix, of the passed filenames are equal.
     *
     * @param string $filename1 The first filename to compare
     * @param string $filename2 The second filename to compare
     *
     * @return boolean TRUE if the passed files basename are equal, else FALSE
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::isEqualFilename()
     */
    protected function isEqualFilename(string $filename1, string $filename2) : bool
    {
        return $this->stripSuffix($filename1, $this->getSuffix()) === $this->stripSuffix($filename2, $this->getOkFileSuffix());
    }

    /**
     * Strips the passed suffix, including the (.), from the filename and returns it.
     *
     * @param string $filename The filename to return the suffix from
     * @param string $suffix   The suffix to return
     *
     * @return string The filname without the suffix
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::stripSuffix()
     */
    protected function stripSuffix(string $filename, string $suffix) : string
    {
        return basename($filename, sprintf('.%s', $suffix));
    }

    /**
     * Prepares and returns an OK filename from the passed parts.
     *
     * @param array $parts The parts to concatenate the OK filename from
     *
     * @return string The OK filename
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::prepareOkFilename()
     */
    protected function prepareOkFilename(array $parts) : string
    {
        return sprintf('%s/%s.%s', $this->getSourceDir(), implode($this->getElementSeparator(), $parts), $this->getOkFileSuffix());
    }

    /**
     * Returns the delement separator char.
     *
     *  @return string The element separator char
     */
    protected function getElementSeparator() : string
    {
        return $this->getFileResolverConfiguration()->getElementSeparator();
    }

    /**
     * Returns the elements the filenames consists of.
     *
     * @return array The array with the filename elements
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::getPatternElements()
     */
    protected function getPatternElements() : array
    {
        return $this->getFileResolverConfiguration()->getPatternElements();
    }

    /**
     * Returns the elements the filenames consists of.
     *
     * @return array The array with the filename elements
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::getOkFileSuffix()
     */
    protected function getOkFileSuffix() : array
    {
        return $this->getFileResolverConfiguration()->getOkFileSuffix();
    }

    /**
     * Returns the actual source directory to load the files from.
     *
     * @return string The actual source directory
     */
    protected function getSourceDir() : string
    {
        throw new \Exception(sprintf('Method "%s" has not been implemented yet', __METHOD__));
    }

    /**
     * Returns the elements the filenames consists of, converted to lowercase.
     *
     * @return array The array with the filename elements
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::getPatternKeys()
     */
    protected function getPatternKeys() : array
    {

        // load the pattern keys from the configuration
        $patternKeys = $this->getPatternElements();

        // make sure that they are all lowercase
        array_walk($patternKeys, function (&$value) {
            $value = strtolower($value);
        });

        // return the pattern keys
        return $patternKeys;
    }

    /**
     * Return's an array with the names of the expected OK files for the actual subject.
     *
     * @return array The array with the expected OK filenames
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::getOkFilenames()
     */
    protected function getOkFilenames() : array
    {

        // initialize the array for the available okFilenames
        $okFilenames = array();

        // prepare the OK filenames based on the found CSV file information
        for ($i = 1; $i <= sizeof($patternKeys = $this->getPatternKeys()); $i++) {
            // intialize the array for the parts of the names (prefix, filename + counter)
            $parts = array();
            // load the parts from the matches
            for ($z = 0; $z < $i; $z++) {
                // append the part
                $parts[] = $this->getLoader()->getMatch($patternKeys[$z]);
            }

            // query whether or not, the OK file exists, if yes append it
            if (file_exists($okFilename = $this->prepareOkFilename($parts))) {
                $okFilenames[] = $okFilename;
            }
        }

        // prepare and return the pattern for the OK file
        return $okFilenames;
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
     * Set's the file resolver configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface $fileResolverConfiguration The file resolver configuration
     */
    public function setFileResolverConfiguration(FileResolverConfigurationInterface $fileResolverConfiguration) : void
    {
        $this->fileResolverConfiguration = $fileResolverConfiguration;
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
     * Query whether or not, the passed CSV filename is in the OK file. If the filename was found,
     * the OK file will be cleaned-up.
     *
     * @param string $filename The filename to be cleaned-up
     *
     * @return void
     * @throws \Exception Is thrown, if the passed filename is NOT in the OK file or the OK can not be cleaned-up
     */
    public function cleanUpOkFile(string $filename) : void
    {

        // query whether or not the subject needs an OK file, if yes remove the filename from the file
        if ($this->getSubjectConfiguration()->isOkFileNeeded() === false) {
            return;
        }

        try {
            // try to load the expected OK filenames
            if (sizeof($okFilenames = $this->getOkFilenames()) === 0) {
                throw new MissingOkFileException(sprintf('Can\'t find a OK filename for file %s', $filename));
            }

            // iterate over the found OK filenames (should usually be only one, but could be more)
            foreach ($okFilenames as $okFilename) {
                // clear the filecache
                \clearstatcache();
                // if the OK filename matches the CSV filename AND the OK file is empty
                if ($this->isEqualFilename($filename, $okFilename) && filesize($okFilename) === 0) {
                    unlink($okFilename);
                    return;
                }

                // else, remove the CSV filename from the OK file
                $this->removeLineFromFile(basename($filename), $fh = fopen($okFilename, 'r+'));
                fclose($fh);

                // if the OK file is empty, delete the file
                if (filesize($okFilename) === 0) {
                    unlink($okFilename);
                }

                // return immediately
                return;
            }

            // throw an exception if either no OK file has been found,
            // or the CSV file is not in one of the OK files
            throw new \Exception(
                sprintf(
                    'Can\'t found filename %s in one of the expected OK files: %s',
                    $filename,
                    implode(', ', $okFilenames)
                )
            );
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
