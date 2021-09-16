<?php

/**
 * TechDivision\Import\Subjects\CastValueSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

/**
 * The interface for all EAV subject implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CastValueSubjectInterface
{

    /**
     * Cast's the passed value based on the backend type information.
     *
     * @param string $backendType The backend type to cast to
     * @param mixed  $value       The value to be casted
     *
     * @return mixed The casted value
     */
    public function castValueByBackendType($backendType, $value);
}
