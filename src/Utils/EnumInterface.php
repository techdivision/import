<?php

/**
 * TechDivision\Import\Utils\UrlRewriteEntityTypes
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * The entity types for the URL rewrite handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EnumInterface
{

    /**
     * Query whether or not the passed value is valid.
     *
     * @param string $value Thevalue to query for
     *
     * @return boolean TRUE if the value is valid, else FALSE
     */
    public function isValid($value) : bool;

    /**
     * Query whether or not the actual instance has the passed value.
     *
     * @param string $value The value to query for
     *
     * @return bool TRUE if the instance equals the passed value, else FALSE
     */
    public function equals($value) : bool;

    /**
     * Return's the enum's value.
     *
     * @return string The enum's value
     */
    public function __toString() : string;
}
