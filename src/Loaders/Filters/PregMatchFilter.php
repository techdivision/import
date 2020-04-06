<?php

/**
 * TechDivision\Import\Loaders\Filters\PregMatchFilter
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

/**
 * Callback that can be used to sort strings in an ascending order.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PregMatchFilter implements PregMatchFilterInterface
{

    /**
     * The pattern to search for, as a string.
     *
     * @var string
     */
    private $pattern = '/.*/';

    /**
     * The flags to optimize the pattern search.
     *
     * @var integer
     */
    private $flags = 0;

    /**
     * The offset with the alternate place from which to start the search (in bytes).
     *
     * @var integer
     */
    private $offset = 0;

    /**
     * The array with the stacked matches.
     *
     * @var array
     */
    protected $matches = array();

    /**
     * Initializes the filter with pattern as well as the flags and the offset to use.
     *
     * @param string $pattern The pattern to search for, as a string
     * @param int    $flags   The flags to optimize the pattern search
     * @param int    $offset  The offset with the alternate place from which to start the search (in bytes)
     */
    public function __construct(string $pattern = '/.*/', int $flags = 0, int $offset = 0)
    {
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->offset = $offset;
    }

    /**
     * Retrn's the pattern to search for, as a string.
     *
     * @return string The pattern
     */
    protected function getPattern() : string
    {
        return $this->pattern;
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
     * The filter's unique name.
     *
     * @return string The unique name
     */
    public function getName() : string
    {
        return (string) PregMatchFilter::class;
    }

    /**
     * Return's the flag used to define what will be passed to the callback invoked
     * by the `array_filter()` method.
     *
     * @return int The flag
     */
    public function getFlag() : int
    {
        return 0;
    }

    /**
     * Return's the number of matches found.
     *
     * @return int The number of matches
     */
    public function countMatches() : int
    {
        return sizeof($this->matches);
    }

    /**
     * Reset's the file resolver to parse another source directory for new files.
     *
     * @return void
     */
    public function reset() : void
    {
        $this->matches = array();
    }

    /**
     * Return's the matches.
     *
     * @return array The array with the matches
     */
    public function getMatches() : array
    {
        return $this->matches;
    }

    /**
     * Adds the passed match to the array with the matches.
     *
     * @param array $matches The matchs itself
     *
     * @return void
     */
    public function addMatches(array $matches) : void
    {

        // lowercase all values of the passed match
        array_walk($matches, function (&$val, &$key) {
            strtolower($key);
        });

        // add the match
        $this->matches[] = $matches;
    }

    /**
     * Query's wether or not the value of the key with the passed name out of the matches
     * with the passed key exists.
     *
     * @param string      $name The name of the match with the given key that has to be queried for
     * @param string|null $key  The key of the match to query the value for
     *
     * @return bool TRUE if the match with the passed name and key is available, else FALSE
     */
    public function hasMatch(string $name, string $key = null) : bool
    {
        return isset($this->matches[$key ?? sizeof($this->matches) - 1][$name]);
    }

    /**
     * Return's the value of the key with the passed name out of the matches
     * with the passed key.
     *
     * @param string      $name The name of the match with the given key that has to be to returned
     * @param string|null $key  The key of the match to return the value for
     *
     * @return string The match itself
     * @throws \InvalidArgumentException Is thrown the value of the match with the passed name and key is not available
     */
    public function getMatch(string $name, string $key = null) : string
    {

        // use the passed key or the key of the last match
        $key = $key ?? sizeof($this->matches) - 1;

        // query whether or not a match is available
        if (isset($this->matches[$key][$name])) {
            return $this->matches[$key][$name];
        }

        // is thrown, if the value can not be loaded
        throw new \InvalidArgumentException(sprintf('Can\'t load match with key "%s" and name "%s"', $key, $name));
    }

    /**
     * Compare's that passed strings binary safe and return's an integer, depending on the comparison result.
     *
     * @param string $v The key that should be used for filtering
     *
     * @return int Returns 1 if the pattern matches given subject, 0 if it does not
     * @throws \RuntimeException Is thrown, if the pattern can not be evaluated against the passed subject
     * @link https://www.php.net/preg_match
     */
    public function __invoke(string $v) : bool
    {

        // initialize the array for the matches
        $matches = array();

        // query whether or not the passed subject matches the pattern
        $result = preg_match($pattern = $this->getPattern(), $v, $matches, $this->getFlags(), $this->getOffset());

        // throw an exception if the pattern can not be evaluated against the passed subject
        if ($result === false) {
            throw new \RuntimeException(sprintf('Pattern "%s" can not be matched against subject "%s"', $pattern, $v));
        }

        // add the matches to the stack, if the pattern matches
        if ($result === 1) {
            $this->addMatches($matches);
        }

        // return the result
        return (bool) $result;
    }
}
