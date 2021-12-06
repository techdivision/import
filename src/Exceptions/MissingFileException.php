<?php

/**
 * TechDivision\Import\Exceptions\MissingFileException
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Exceptions;

/**
 * A exception that is thrown if a import file is NOT empty, e. g. before it should be deleted.
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MissingFileException extends \Exception
{
}
