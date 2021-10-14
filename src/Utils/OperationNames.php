<?php

/**
 * TechDivision\Import\Utils\OperationNames
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class containing the operation names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OperationNames
{

    /**
     * The operation key for a row that has to be created.
     *
     * @var string
     */
    const CREATE = 'create';

    /**
     * The operation key for a row that has to be updated.
     *
     * @var string
     */
    const UPDATE = 'update';

    /**
     * The operation key for a row that has to be deleted.
     *
     * @var string
     */
    const DELETE = 'delete';

    /**
     * The operation key for a row that has to be skipped.
     *
     * @var string
     */
    const SKIP = 'skip';
}
