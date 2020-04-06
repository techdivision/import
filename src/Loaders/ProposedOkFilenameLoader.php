<?php

/**
 * TechDivision\Import\Loaders\ProposedOkFilenameLoader
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

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Loaders\Sorters\UasortImpl;
use TechDivision\Import\Loaders\Filters\PregMatchFilter;
use TechDivision\Import\Loaders\Filters\FilterImplInterface;
use TechDivision\Import\Loaders\Sorters\SorterImplInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Loader implementation that loads a list of proposed .OK filenames and their
 * matching CSV files, based on the found CSV files that'll be loaded by the
 * passed loader instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/array_filter
 */
class ProposedOkFilenameLoader extends FilteredLoader implements FilteredLoaderInterface, SortedLoaderInterface
{

    /**
     * The regular expression used to load the files with.
     *
     * @var string
     */
    private $regex = '/^.*\/%s\\.%s$/';

    /**
     * The actual source directory to use.
     *
     * @var string
     */
    private $sourceDir;

    /**
     * The file resolver configuration instance.
     *
     * @var \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface
     */
    private $fileResolverConfiguration;

    /**
     * The sorter instance to use.
     *
     * @var \TechDivision\Import\Loaders\Sorters\SorterImplInterface
     */
    private $sorterImpl;

    /**
     * Initializes the file handler instance.
     *
     * @param \TechDivision\Import\Loaders\FilteredLoaderInterface     $loader     The parent loader instance
     * @param \TechDivision\Import\Loaders\Filters\FilterImplInterface $filterImpl The filter instance to use
     * @param \TechDivision\Import\Loaders\Sorters\SorterImplInterface $sorterImpl The sorter instance to use
     */
    public function __construct(
        FilteredLoaderInterface $loader,
        FilterImplInterface $filterImpl = null,
        SorterImplInterface $sorterImpl = null
    ) {

        // initialize the sorter instance
        $this->sorterImpl = $sorterImpl ?? new UasortImpl();

        // pass parent loader and filter instance to the parent constructor
        parent::__construct($loader, $filterImpl);
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
     * Return's the sorter instance to use.
     *
     * @return \TechDivision\Import\Loaders\Sorters\SorterImplInterface The sorter instance to use
     */
    protected function getSorterImpl() : SorterImplInterface
    {
        return $this->sorterImpl;
    }

    /**
     * Returns the regular expression used to load the files with.
     *
     * @return string The regular expression
     */
    protected function getRegex() : string
    {
        return $this->regex;
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
     * Returns the suffix for the import files.
     *
     * @return string The suffix
     */
    protected function getSuffix() : string
    {
        return $this->getFileResolverConfiguration()->getSuffix();
    }

    /**
     * Returns the elements the filenames consists of.
     *
     * @return array The array with the filename elements
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::getOkFileSuffix()
     */
    protected function getOkFileSuffix() : string
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
        return $this->sourceDir;
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
     * Resolves the pattern value for the given element name.
     *
     * @param string $element The element name to resolve the pattern value for
     *
     * @return string The resolved pattern value
     * @throws \InvalidArgumentException Is thrown, if the element value can not be loaded from the file resolver configuration
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::resolvePatternValue()
     */
    protected function resolvePatternValue(string $element) : string
    {

        // query whether or not matches has been found OR the counter element has been passed
        if ($this->getLoader()->countMatches() === 0 || BunchKeys::COUNTER === $element) {
            // prepare the method name for the callback to load the pattern value with
            $methodName = sprintf('get%s', ucfirst($element));

            // load the pattern value
            if (in_array($methodName, get_class_methods($this->getFileResolverConfiguration()))) {
                return call_user_func(array($this->getFileResolverConfiguration(), $methodName));
            }

            // stop processing and throw an exception
            throw new \InvalidArgumentException('Can\'t load pattern value for element "%s" from file resolver configuration', $element);
        }

        // try to load the pattern value from the matches
        return $this->getLoader()->getMatch($element);
    }

    /**
     * Returns the values to create the regex pattern from.
     *
     * @return array The array with the pattern values
     * @todo Refactorig required, because of duplicate method
     * @see \TechDivision\Import\Loaders\Filters\OkFileFilter::resolvePatternValues()
     */
    protected function resolvePatternValues() : array
    {

        // initialize the array
        $elements = array();

        // prepare the pattern values
        foreach ($this->getPatternKeys() as $element) {
            $elements[] = sprintf('(?<%s>%s)', $element, $this->resolvePatternValue($element));
        }

        // return the pattern values
        return $elements;
    }

    /**
     * Prepares and returns the pattern for the regex to load the files from the
     * source directory for the passed subject.
     *
     * @return string The prepared regex pattern
     */
    protected function getPattern() : string
    {
        return sprintf(
            $this->getRegex(),
            implode($this->getElementSeparator(), $this->resolvePatternValues()),
            $this->getSuffix()
        );
    }

    /**
     * Returns the match with the passed name.
     *
     * @param array $keys The keys of the match to return
     *
     * @return string|null The match itself
     */
    protected function proposedOkFilenamesByKeys(array $keys) : array
    {

        // intialize the array for the .OK filename => .CSV filenames
        $result = array();

        // load the matches from the preg match filesystem loader
        $matches = $this->getLoader()->getMatches();

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
                $okFilename = implode($this->getElementSeparator(), $element);
                // create the path to the .OK file
                $okPath = sprintf('%s/%s.%s', $sourceDir, $okFilename, $this->getOkFileSuffix());
                // register the proposed .OK file in the array
                $result[$okPath][] = basename($csvPath);
            }
        }

        // return the array with the result
        return $result;
    }

    /**
     * Return's an array with the proposed .OK filenames as key and the matching CSV
     * files as values for the import with the passed serial.
     *
     * Based on the passed serial, the files with the configured prefix from the also
     * configured source directory will be loaded and processed to return the array
     * with the proposed .OK filenames.
     *
     * @return array The array with key => value pairs of the proposed .OK and the matching CSV files
     */
    protected function propsedOkFilenames() : array
    {

        // initialize the array for the available okFilenames
        $okFilenames = array();

        // load the pattern keys (prefix, filename and suffix),
        // that has to be used from the configuration
        $patternKeys = $this->getPatternKeys();

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

        // prepare and return the pattern for the OK file
        return $okFilenames;
    }

    /**
     * Return's the actual source directory.
     *
     * @param string $sourceDir The actual source directory
     *
     * @return void
     */
    public function setSourceDir(string $sourceDir) : void
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Add's the passed sorter to the loader instance.
     *
     * @param callable $sorter The sorter to add
     *
     * @return void
     */
    public function addSorter(callable $sorter) : void
    {
        $this->getSorterImpl()->addSorter($sorter);
    }

    /**
     * Return's the array with the sorter callbacks.
     *
     * @return callable[] The sorter callbacks
     */
    public function getSorters() : array
    {
        return $this->getSorterImpl()->getSorters();
    }

    /**
     * Set's the file resolver configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface $fileResolverConfiguration The file resolver configuration
     *
     * @return void
     */
    public function setFileResolverConfiguration(FileResolverConfigurationInterface $fileResolverConfiguration) : void
    {
        $this->fileResolverConfiguration = $fileResolverConfiguration;
    }

    /**
     * Loads, sorts and returns the files by using the passed glob pattern.
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

        // load the pattern to filter the available CSV files with. We filter
        // the .CSV files to only get those that matches the prefix.
        $this->getLoader()->addFilter(new PregMatchFilter($this->getPattern()));

        // load the available CSV files and initialize the matches that
        // we need to create the list with the proposed .OK files
        $this->getLoader()->load($pattern);

        // initialize the array for the available okFilenames
        $okFilenames = array();

        // load the pattern keys (prefix, filename and suffix),
        // that has to be used from the configuration
        $patternKeys = $this->getPatternKeys();

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

        // finally, we filter and sort them with the configured behaviour
        $this->getFilterImpl()->filter($okFilenames);
        $this->getSorterImpl()->sort($okFilenames);

        // prepare and return the pattern for the OK file
        return $okFilenames;
    }
}
