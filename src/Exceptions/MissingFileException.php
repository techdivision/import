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

use Throwable;

/**
 * A exception that is thrown if a import file is missing or the prefix does not correspond Naming convention
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MissingFileException extends \Exception
{
    const NOT_FOUND_CODE = 4;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @param string         $message  [optional] The Exception message to throw.
     * @param int            $code     [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct($message = "", $code = self::NOT_FOUND_CODE, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
