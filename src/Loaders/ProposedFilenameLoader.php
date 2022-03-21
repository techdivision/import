<?php

/**
 * TechDivision\Import\Loaders\ProposedFilenameLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Generic loader implementation for a proposed filename
 * based on the given filresolver configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProposedFilenameLoader implements LoaderInterface
{

    /**
     * The pattern to search for, as a string.
     *
     * @var string
     */
    protected $pattern = '/^.*\/%s\\.%s$/';

    /**
     * The file resolver configuration
     *
     * @var FileResolverConfigurationInterface
     */
    protected $fileResolverConfiguration;

    /**
     * The counter used to prepare the proposed filename.
     *
     * @var integer
     */
    protected $counter = 0;

    /**
     * The flags to optimize the pattern search.
     *
     * @var integer
     */
    protected $flags = 0;

    /**
     * The offset with the alternate place from which to start the search (in bytes).
     *
     * @var integer
     */
    protected $offset = 0;

    /**
     * Initializes the filter with pattern as well as the flags and the offset to use.
     *
     * @param string $pattern The pattern to search for, as a string
     * @param int    $flags   The flags to optimize the pattern search
     * @param int    $offset  The offset with the alternate place from which to start the search (in bytes)
     */
    public function __construct(string $pattern = '/^.*\/%s\\.%s$/', int $flags = 0, int $offset = 0)
    {
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->offset = $offset;
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
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    public function getFileResolverConfiguration() : FileResolverConfigurationInterface
    {
        return $this->fileResolverConfiguration;
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
     * Returns the elements the filenames consists of, converted to lowercase.
     *
     * @return array The array with the filename elements
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
     * Returns's the prefix from the fileresolver, in case one has specified,
     * otherwhise return the default prefix.
     *
     * @return string The prefix found in the subject's fileresolver configuration
     */
    protected function getPrefix() : string
    {
        return $this->getFileResolverConfiguration()->hasPrefix() ? $this->getFileResolverConfiguration()->getPrefix() : 'product-import';
    }

    /**
     * Returns's the filena,e from the fileresolver, in case one has specified,
     * otherwhise return the default filename, which is the current timestamp.
     *
     * @return string The filename found in the subject's fileresolver configuration
     */
    protected function getFilename() : string
    {
        return $this->getFileResolverConfiguration()->hasFilename() ? $this->getFileResolverConfiguration()->getFilename() : date('Ymd-His');
    }

    /**
     * Returns's the prefix from the fileresolver, in case one has specified,
     * otherwhise return the default counter.
     *
     * @return string The counter found in the subject's fileresolver configuration
     */
    protected function getCounter() : string
    {
        return $this->getFileResolverConfiguration()->hasCounter() ? $this->getFileResolverConfiguration()->getCounter() : str_pad(++$this->counter, 2, 0, STR_PAD_LEFT);
    }

    /**
     * Resolves the pattern value for the given element name.
     *
     * @param string $element The element name to resolve the pattern value for
     *
     * @return string The resolved pattern value
     * @throws \InvalidArgumentException Is thrown, if the element value can not be loaded from the file resolver configuration
     */
    protected function resolveValue(string $element) : string
    {

        // load the pattern value
        if (in_array($methodName = sprintf('get%s', ucfirst($element)), get_class_methods($this))) {
            return call_user_func(array($this, $methodName));
        }

        // stop processing and throw an exception
        throw new \InvalidArgumentException('Can\'t load pattern value for element "%s" from file resolver configuration', $element);
    }

    /**
     * Returns the values to create the regex pattern from.
     *
     * @return array The array with the pattern values
     */
    protected function resolveValues() : array
    {

        // initialize the array
        $elements = array();

        // prepare the pattern values
        foreach ($this->getPatternKeys() as $element) {
            $elements[] = $this->resolveValue($element);
        }

        // return the pattern values
        return $elements;
    }

    /**
     * Resolves the pattern value for the given element name.
     *
     * @param string $element The element name to resolve the pattern value for
     *
     * @return string The resolved pattern value
     * @throws \InvalidArgumentException Is thrown, if the element value can not be loaded from the file resolver configuration
     */
    protected function resolvePatternValue(string $element) : string
    {

        // prepare the method name for the callback to load the pattern value with
        $methodName = sprintf('get%s', ucfirst($element));

        // load the pattern value
        if (in_array($methodName, get_class_methods($this->getFileResolverConfiguration()))) {
            return call_user_func(array($this->getFileResolverConfiguration(), $methodName));
        }

        // stop processing and throw an exception
        throw new \InvalidArgumentException('Can\'t load pattern value for element "%s" from file resolver configuration', $element);
    }

    /**
     * Returns the values to create the regex pattern from.
     *
     * @return array The array with the pattern values
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
            $this->pattern,
            implode($this->getElementSeparator(), $this->resolvePatternValues()),
            $this->getSuffix()
        );
    }

    /**
     * Return's the flags to optimize the pattern search.
     *
     * @return int The flags
     */
    protected function getFlags() : int
    {
        return $this->flags;
    }

    /**
     * Return's the offset with the alternate place from which to start the search (in bytes).
     *
     * @return int The offset in bytes
     */
    protected function getOffset() : int
    {
        return $this->offset;
    }

    /**
     * Loads, sorts and returns the files by using the passed glob pattern.
     *
     * If no pattern will be passed to the `load()` method, the files of
     * the actual directory using `getcwd()` will be returned.
     *
     * @param string|null $filename The pattern to load the files from the filesystem
     *
     * @return array The array with the data
     */
    public function load($filename = null) : string
    {

        // initialize the array with the matches
        $matches = array();

        // query whether or not the passed subject matches the pattern
        if (preg_match($this->getPattern(), $filename, $matches, $this->getFlags(), $this->getOffset())) {
            return $filename;
        }

        // initialize the pattern values
        $elements = $this->resolveValues();
        // create the filename by concatenating the element values
        $newFilename = implode($this->getElementSeparator(), $elements);
        // create the path to the .OK file
        return sprintf('%s.%s', $newFilename, $this->getSuffix());
    }
}
