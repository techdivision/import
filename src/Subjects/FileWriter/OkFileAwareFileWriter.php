<?php

/**
 * TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
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
use TechDivision\Import\Subjects\FileWriter\Filters\FileWriterFilterInterface;
use TechDivision\Import\Subjects\FileWriter\Sorters\FileWriterSorterInterface;

/**
 * Plugin that processes the subjects.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileAwareFileWriter implements FileWriterInterface
{

    /**
     * The file resolver instance to loads the CSV files.
     *
     * @var \TechDivision\Import\Subjects\FileResolver\FileResolverInterface
     */
    private $fileResolver;

    /**
     * The array with the filters to filter the loaded CSV files.
     *
     * @var \TechDivision\Import\Subjects\FileWriter\Filters\FileWriterFilterInterface[]
     */
    private $filters = array();

    /**
     * The array with the sorters to sort the loaded CSV files.
     *
     * @var \TechDivision\Import\Subjects\FileWriter\Sorters\FileWriterSorterInterface[]
     */
    private $sorters = array();

    /**
     * Returns the match with the passed name.
     *
     * @param array $keys The keys of the match to return
     *
     * @return string|null The match itself
     */
    protected function proposedOkFilenamesByKeys(array $keys) : array
    {

        // load the file resolver instance
        $fileResolver = $this->getFileResolver();

        // intialize the array for the .OK filename => .CSV filenames
        $result = array();

        // load the matches from the fileresolver
        $matches = $fileResolver->getMatches();

        // process the matches
        foreach ($matches as $i => $match) {
            // initialize the array for the values of the match elements
            $elements = array();
            // load the values from the matches
            foreach ($keys as $key) {
                if (array_key_exists($key, $match)) {
                    $elements[$i][] = $match[$key];
                }
            }

            // use the values to create the proposed .OK filenames
            foreach ($elements as $element) {
                // load the source directory
                $sourceDir = dirname($csvPath = $match[0]);
                // create the filename by concatenating the element values
                $okFilename = implode($fileResolver->getElementSeparator(), $element);
                // create the path to the .OK file
                $okPath = sprintf('%s/%s.%s', $sourceDir, $okFilename, $fileResolver->getOkFileSuffix());
                // register the proposed .OK file in the array
                $result[$okPath][] = basename($csvPath);
            }
        }

        // return the array with the result
        return $result;
    }

    /**
     * Add's the passed filter to the writer instance.
     *
     * @param \TechDivision\Import\Subjects\FileWriter\Filters\FileWriterFilterInterface $filter The filter to add
     *
     * @return void
     */
    public function addFilter(FileWriterFilterInterface $filter) : void
    {
        $this->filters[] = $filter;
    }

    /**
     * Adds the passed sorter to the writer instance.
     *
     * @param \TechDivision\Import\Subjects\FileWriter\Sorters\FileWriterSorterInterface $sorter The sorter to add
     */
    public function addSorter(FileWriterSorterInterface $sorter) : void
    {
        $this->sorters[] = $sorter;
    }

    /**
     * Set's the file resolver intance.
     *
     * @param \TechDivision\Import\Subjects\FileResolver\FileResolverInterface $fileResolver The file resolver instance
     *
     * @return void
     */
    public function setFileResolver(FileResolverInterface $fileResolver) : void
    {
        $this->fileResolver = $fileResolver;
    }

    /**
     * Return's the file resolver instance.
     *
     * @return \TechDivision\Import\Subjects\FileResolver\FileResolverInterface The file resolver instance
     */
    public function getFileResolver() : FileResolverInterface
    {
        return $this->fileResolver;
    }

    /**
     * Create's the .OK files for the import with the passed serial.
     *
     * @param string $serial The serial to create the .OK files for
     *
     * @return int Return's the number of created .OK files
     * @throws \Exception Is thrown, one of the proposed .OK files can not be created
     */
    public function createOkFiles(string $serial) : int
    {

        // initialize the counter for the processed .OK files
        $counter = 0;

        // load the array with the proposed .OK filenames
        $proposedOkFilenames = $this->propsedOkFilenames($serial);

        // create the proposed .OK files
        foreach ($proposedOkFilenames as $okFilename => $csvFilenames) {
            // write the proposed .OK file
            if ($this->getFileResolver()->getFilesystemAdapter()->write($okFilename, implode(PHP_EOL, $csvFilenames)) === false) {
                throw new \Exception(sprintf('Can\' create any .OK file "%s" for serial "%s"', $okFilename, $serial));
            }
            // raise the counter
            $counter++;
        }

        // return the number of created .OK files
        return $counter;
    }

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
    public function propsedOkFilenames(string $serial) : array
    {

        // load the file information
        $this->loadFiles($serial);

        // initialize the array for the available okFilenames
        $okFilenames = array();

        // load the pattern keys (prefix, filename and suffix),
        // that has to be used from the configuration
        $patternKeys = $this->getFileResolver()->getPatternKeys();

        // prepare the .OK filenames based on the found CSV file information
        for ($i = 1; $i <= sizeof($patternKeys); $i++) {
            // initialize the array for the pattern that has to used to load
            // the propsed .OK file for
            $keys = array();
            // load the parts from the matches
            for ($z = 0; $z < $i; $z++) {
                $keys[] = $patternKeys[$z];
            }
            // merge the proposed .OK filenames for the passed keys into the array
            $okFilenames = array_merge_recursive($okFilenames, $this->proposedOkFilenamesByKeys($keys));
        }

        // sort the .OK filenames with the registered sorters
        foreach ($this->sorters as $sorter) {
            uasort($okFilenames, $sorter);
        }

        // filter the .OK filenames with the registered filters
        foreach ($this->filters as $filter) {
            $okFilenames = array_filter($okFilenames, $filter, $filter->getFlags());
        }

        // prepare and return the pattern for the OK file
        return $okFilenames;
    }

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     */
    public function loadFiles(string $serial) : array
    {

        // load the file resolver instance
        $fileResolver = $this->getFileResolver();

        // initialize the array with the files that has to be handled
        $filesToHandle = array();

        // load the files that match the regular expressiong
        $files = $fileResolver->loadFiles($serial);

        // load the pattern to load the available CSV files, do this here and not after each found file again,
        // because we want to load ALL available CSV files with the given prefix and not bunches only!
        $pattern = $fileResolver->preparePattern();

        // query whether or not the file has to be handled
        foreach ($files as $file) {
            // initialize the array with the matches
            $matches = array();
            // update the matches, if the pattern matches
            if (preg_match($pattern, $file, $matches)) {
                // add the match
                $fileResolver->addMatch($matches);
                // append the file to the list of files that has to be handled
                $filesToHandle[] = $file;
            }
        }

        // return the array with the files
        return $filesToHandle;
    }
}
