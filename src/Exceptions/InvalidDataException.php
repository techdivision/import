<?php

/**
 * TechDivision\Import\Exceptions\InvalidDataException
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
 * A exception that is thrown if a import file with invalid Data.
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class InvalidDataException extends \Exception
{
    const INVALID_DATA_CODE = 13;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = self::INVALID_DATA_CODE, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
