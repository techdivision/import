<?php

/**
 * TechDivision\Import\Subjects\FileResolver\BunchFileResolver
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileResolver;

use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Exceptions\MissingOkFileException;

/**
 * Plugin that processes the subjects.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleFileResolver extends AbstractFileResolver
{

    /**
     * The regular expression used to load the files with.
     *
     * @var string
     */
    private $regex = '/^.*\/%s\\.%s$/';

    /**
     * The matches for the last processed CSV filename.
     *
     * @var array
     */
    private $matches = array();

    /**
     * Returns the regular expression used to load the files with.
     *
     * @return string The regular expression
     */
    protected function getRegex()
    {
        return $this->regex;
    }

    /**
     * Returns the number of matches found.
     *
     * @return integer The number of matches
     */
    protected function countMatches()
    {
        return sizeof($this->matches);
    }

    /**
     * Adds the passed match to the array with the matches.
     *
     * @param string $name  The name of the match
     * @param string $match The match itself
     *
     * @return void
     */
    protected function addMatch($name, $match)
    {
        $this->matches[strtolower($name)] = $match;
    }

    /**
     * Returns the match with the passed name.
     *
     * @param string $name The name of the match to return
     *
     * @return string|null The match itself
     */
    protected function getMatch($name)
    {
        if (isset($this->matches[$name])) {
            return $this->matches[$name];
        }
    }

    /**
     * Returns the elements the filenames consists of, converted to lowercase.
     *
     * @return array The array with the filename elements
     */
    protected function getPatternKeys()
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
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    protected function removeLineFromFile($line, $filename)
    {
        $this->getApplication()->removeLineFromFile($line, $filename);
    }

    /**
     * Returns the values to create the regex pattern from.
     *
     * @return array The array with the pattern values
     */
    protected function resolvePatternValues()
    {

        // initialize the array
        $elements = array();

        // load the pattern keys
        $patternKeys = $this->getPatternKeys();

        // prepare the pattern values
        foreach ($patternKeys as $element) {
            $elements[] = sprintf('(?<%s>%s)', $element, $this->resolvePatternValue($element));
        }

        // return the pattern values
        return $elements;
    }

    /**
     * Resolves the pattern value for the given element name.
     *
     * @param string $element The element name to resolve the pattern value for
     *
     * @return string|null The resolved pattern value
     */
    protected function resolvePatternValue($element)
    {

        // query whether or not matches has been found OR the counter element has been passed
        if ($this->countMatches() === 0 || BunchKeys::COUNTER === $element) {
            // prepare the method name for the callback to load the pattern value with
            $methodName = sprintf('get%s', ucfirst($element));

            // load the pattern value
            if (in_array($methodName, get_class_methods($this->getFileResolverConfiguration()))) {
                return call_user_func(array($this->getFileResolverConfiguration(), $methodName));
            }

            // stop processing
            return;
        }

        // try to load the pattern value from the matches
        return $this->getMatch($element);
    }

    /**
     * Prepares and returns the pattern for the regex to load the files from the
     * source directory for the passed subject.
     *
     * @return string The prepared regex pattern
     */
    protected function preparePattern()
    {
        return sprintf($this->getRegex(), implode($this->getElementSeparator(), $this->resolvePatternValues()), $this->getSuffix());
    }

    /**
     * Prepares and returns an OK filename from the passed parts.
     *
     * @param array $parts The parts to concatenate the OK filename from
     *
     * @return string The OK filename
     */
    protected function prepareOkFilename(array $parts)
    {
        return sprintf('%s/%s.%s', $this->getSourceDir(), implode($this->getElementSeparator(), $parts), $this->getOkFileSuffix());
    }

    /**
     * Query whether or not the basename, without suffix, of the passed filenames are equal.
     *
     * @param string $filename1 The first filename to compare
     * @param string $filename2 The second filename to compare
     *
     * @return boolean TRUE if the passed files basename are equal, else FALSE
     */
    protected function isEqualFilename($filename1, $filename2)
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
    protected function stripSuffix($filename, $suffix)
    {
        return basename($filename, sprintf('.%s', $suffix));
    }

    /**
     * Return's an array with the names of the expected OK files for the actual subject.
     *
     * @return array The array with the expected OK filenames
     */
    protected function getOkFilenames()
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
     * Resets the file resolver to parse another source directory for new files.
     *
     * @return void
     */
    public function reset()
    {
        $this->matches = array();
    }

    /**
     * Returns the matches.
     *
     * @return array The array with the matches
     */
    public function getMatches()
    {
        return array_merge(array(BunchKeys::FILENAME => date('Ymd-His'), BunchKeys::COUNTER => 1), $this->matches);
    }

    /**
     * Queries whether or not, the passed filename should be handled by the subject.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the file should be handled, else FALSE
     */
    public function shouldBeHandled($filename)
    {

        // initialize the array with the matches
        $matches = array();

        // update the matches, if the pattern matches
        if ($result = preg_match($this->preparePattern(), $filename, $matches)) {
            foreach ($matches as $name => $match) {
                $this->addMatch($name, $match);
            }
        }

        // initialize the flag: we assume that the file is in an OK file
        $inOkFile = true;

        // query whether or not the subject requests an OK file
        if ($this->getSubjectConfiguration()->isOkFileNeeded()) {
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
                if ($this->isEqualFilename($filename, $okFilename) && filesize($okFilename) === 0) {
                    $inOkFile = true;
                    break;
                }

                // load the OK file content
                $okFileLines = file($okFilename);

                // remove line breaks
                array_walk($okFileLines, function (&$line) {
                    $line = trim($line, PHP_EOL);
                });

                // query whether or not the OK file contains the filename
                if (in_array(basename($filename), $okFileLines)) {
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
        return ((boolean) $result && $inOkFile);
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
    public function cleanUpOkFile($filename)
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
}
