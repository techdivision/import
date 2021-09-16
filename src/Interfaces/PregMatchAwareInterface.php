<?php

/**
 * TechDivision\Import\Interfaces\PregMatchAwareInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Interfaces;

/**
 * A generic interface for preg match aware implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PregMatchAwareInterface
{

    /**
     * Return's the number of matches found.
     *
     * @return int The number of matches
     */
    public function countMatches() : int;

    /**
     * Reset's the file resolver to parse another source directory for new files.
     *
     * @return void
     */
    public function reset() : void;

    /**
     * Return's the matches.
     *
     * @return array The array with the matches
     */
    public function getMatches() : array;

    /**
     * Adds the passed match to the array with the matches.
     *
     * @param array $matches The matchs itself
     *
     * @return void
     */
    public function addMatches(array $matches) : void;

    /**
     * Query's wether or not the value of the key with the passed name out of the matches
     * with the passed key exists.
     *
     * @param string      $name The name of the match with the given key that has to be queried for
     * @param string|null $key  The key of the match to query the value for
     *
     * @return bool TRUE if the match with the passed name and key is available, else FALSE
     */
    public function hasMatch(string $name, string $key = null) : bool;

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
    public function getMatch(string $name, string $key = null) : string;
}
