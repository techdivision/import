<?php

/**
 * TechDivision\Import\Loaders\Filters\OkFileFilter
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

namespace TechDivision\Import\Loaders\Filters;

use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Handlers\OkFileHandlerInterface;


/**
 * Callback used to filter CSV files based on an availble .OK file and the information found
 * in the matching .OK file.
 *
 * The import process only starts, when an .OK is available in the same directory where
 * the CSV files are located. The naming convention for the .OK MUST follow one of these
 * naming conventions
 *
 * <IMPORT-DIRECTORY>/<PREFIX>.ok
 * <IMPORT-DIRECTORY>/<PREFIX>_<FILENAME>.ok
 * <IMPORT-DIRECTORY>/<PREFIX>_<FILENAME>_<COUNTER>.ok
 *
 * to match the apropriate CSV files.  For example, if you have the CSV file
 *
 * import-cli-simple/projects/sample-data/tmp/magento-import_20170203_01.csv
 *
 * then one of the following .OK files will match
 *
 * import-cli-simple/projects/sample-data/tmp/magento-import.ok
 * import-cli-simple/projects/sample-data/tmp/magento-import_20170203.ok
 * import-cli-simple/projects/sample-data/tmp/magento-import_20170203_01.ok
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileFilter extends PregMatchFilter
{

    /**
     * The .OK file handler instance to use.
     *
     * @var \TechDivision\Import\Handlers\OkFileHandlerInterface
     */
    private $handler;

    /**
     * The subject configuration with the information to load the files that has to be imported with.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * The source dirctory to use.
     *
     * @var string
     */
    private $sourceDir;

    /**
     * Initializes the filter with pattern as well as the flags and the offset to use.
     *
     * @param string $pattern The pattern to search for, as a string
     * @param int    $flags   The flags to optimize the pattern search
     * @param int    $offset  The offset with the alternate place from which to start the search (in bytes)
     */
    public function __construct(
        OkFileHandlerInterface $handler,
        SubjectConfigurationInterface $subjectConfiguration,
        string $sourceDir,
        string $pattern = '/^.*\/%s\\.%s$/',
        int $flags = 0,
        int $offset = 0
    ) {

        // set the passed instances
        $this->handler = $handler;
        $this->sourceDir = $sourceDir;
        $this->subjectConfiguration = $subjectConfiguration;

        // pass the pattern, flags and offset to the parent constructor
        parent::__construct($pattern, $flags, $offset);
    }

    /**
     * Return's the .OK file handler instance.
     *
     * @return \TechDivision\Import\Handlers\OkFileHandlerInterface The .OK file handler instance
     */
    protected function getHandler() : OkFileHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration instance
     */
    protected function getSubjectConfiguration() : SubjectConfigurationInterface
    {
        return $this->subjectConfiguration;
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
     * Returns the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The configuration instance
     */
    protected function getFileResolverConfiguration() : FileResolverConfigurationInterface
    {
        return $this->getSubjectConfiguration()->getFileResolver();
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
     * Returns the suffix for the import files.
     *
     * @return string The suffix
     */
    protected function getSuffix() : string
    {
        return $this->getFileResolverConfiguration()->getSuffix();
    }

    /**
     * Returns the OK file suffix to use.
     *
     * @return string The OK file suffix
     */
    protected function getOkFileSuffix() : string
    {
        return $this->getFileResolverConfiguration()->getOkFileSuffix();
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
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    protected function isOkFileNeeded() : bool
    {
        return $this->getSubjectConfiguration()->isOkFileNeeded();
    }

    /**
     * Query whether or not the basename, without suffix, of the passed filenames are equal.
     *
     * @param string $filename1 The first filename to compare
     * @param string $filename2 The second filename to compare
     *
     * @return boolean TRUE if the passed files basename are equal, else FALSE
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
     */
    protected function stripSuffix(string $filename, string $suffix) : string
    {
        return basename($filename, sprintf('.%s', $suffix));
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
     * Resolves the pattern value for the given element name.
     *
     * @param string $element The element name to resolve the pattern value for
     *
     * @return string The resolved pattern value
     * @throws \InvalidArgumentException Is thrown, if the element value can not be loaded from the file resolver configuration
     */
    protected function resolvePatternValue(string $element) : string
    {

        // query whether or not matches has been found OR the counter element has been passed
        if ($this->countMatches() === 0 || BunchKeys::COUNTER === $element) {
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
        return $this->getMatch($element);
    }

    /**
     * Returns the values to create the regex pattern from.
     *
     * @param array|null $patternKeys The pattern keys used to load the pattern values
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
            parent::getPattern(),
            implode($this->getElementSeparator(), $this->resolvePatternValues()),
            $this->getSuffix()
        );
    }

    /**
     * Prepares and returns an OK filename from the passed parts.
     *
     * @param array $parts The parts to concatenate the OK filename from
     *
     * @return string The OK filename
     */
    protected function prepareOkFilename(array $parts) : string
    {
        return sprintf('%s/%s.%s', $this->getSourceDir(), implode($this->getElementSeparator(), $parts), $this->getOkFileSuffix());
    }

    /**
     * Return's an array with the names of the expected OK files for the actual subject.
     *
     * @return array The array with the expected OK filenames
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
                $parts[] = $this->getMatch($patternKeys[$z]);
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
     * Compare's that passed strings binary safe and return's an integer, depending on the comparison result.
     *
     * @param string $v The key that should be used for filtering
     *
     * @return int Returns 1 if the pattern matches given subject, 0 if it does not
     * @throws \RuntimeException Is thrown, if the pattern can not be evaluated against the passed subject
     * @link http://www.php.net/manual/en/function.strcmp.php
     */
    public function __invoke(string $v) : bool
    {

        // invoke the parent filter callback
        $result = parent::__invoke($v);

        // initialize the flag: we assume that the file is in an OK file
        $inOkFile = true;

        // query whether or not the subject requests an OK file
        if ($this->isOkFileNeeded()) {
            // try to load the expected OK filenames
            if (sizeof($okFilenames = $this->getOkFilenames()) === 0) {
                // stop processing, because the needed OK file is NOT available
                return false;
            }

            // reset the flag: assumption from initialization is invalid now
            $inOkFile = false;

            // iterate over the found OK filenames (should usually be only one, but could be more)
            foreach ($okFilenames as $okFilename) {
                // if the OK filename matches the CSV filename AND the OK file is empty
                if ($this->isEqualFilename($v, $okFilename) && filesize($okFilename) === 0) {
                    $this->getHandler()->delete($okFilename);
                    $inOkFile = true;
                    break;
                }

                // load the OK file content
                $okFileLines = file($okFilename);

                // remove line breaks
                array_walk($okFileLines, function (&$line) {
                    $line = trim($line, PHP_EOL);
                });

                // query whether or not the .OK file contains the filename
                if (in_array(basename($v), $okFileLines)) {
                    $this->getHandler()->cleanUpOkFile($v, $okFilename);
                    $inOkFile = true;
                    break;
                }
            }

            // reset the matches because we've a new bunch
            if ($inOkFile === false) {
                $this->reset();
            }
        }

        // stop processing, because the filename doesn't match the subjects pattern
        return $result && $inOkFile;
    }
}
