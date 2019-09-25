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
    protected function addMatch(array $match)
    {

        array_walk($match, function(&$val, &$key) {
            strtolower($key);
        });

        $this->matches[] = $match;
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

        $lastMatch = $this->matches[sizeof($this->matches) - 1];

        if (isset($lastMatch[$name])) {
            return $lastMatch[$name];
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
     * Queries whether or not, the passed filename should be handled by the subject.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the file should be handled, else FALSE
     */
    protected function shouldBeHandled($filename)
    {

        // initialize the array with the matches
        $matches = array();

        // update the matches, if the pattern matches
        if ($result = preg_match($this->preparePattern(), $filename, $matches)) {
            $this->addMatch($matches);
        }

        // stop processing, because the filename doesn't match the subjects pattern
        return (boolean) $result;
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
        return $this->matches;
    }
}
