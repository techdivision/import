<?php

/**
 * TechDivision\Import\Loaders\PregMatchFilteredLoaderInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Interface for loader implementations that provides a preg match filter functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 * @link      https://www.php.net/array_filter
 */
interface PregMatchFilteredLoaderInterface extends FilteredLoaderInterface
{

    /**
     * Return's the number of matches found.
     *
     * @return int The number of matches
     */
    public function countMatches() : int;

    /**
     * Return's the matches of all filters.
     *
     * @return array The array with the matches
     */
    public function getMatches() : array;

    /**
     * Reset's the registered filters.
     *
     * @return void
     */
    public function reset() : void;

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
