<?php

/**
 * TechDivision\Import\Utils\InputArgumentKeysInterface
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

namespace TechDivision\Import\Utils;

/**
 * Interface for classes containing the available input argument keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface InputArgumentKeysInterface extends \ArrayAccess
{

    /**
     * The input argument key for the shortcut to execute.
     *
     * @var string
     */
    const SHORTCUT = 'shortcut';

    /**
     * The input argument key for the operation names to use.
     *
     * @var string
     */
    const OPERATION_NAMES = 'operation-names';

    /**
     * The input argument key for the entity type code to use.
     *
     * @var string
     */
    const ENTITY_TYPE_CODE = 'entity-type-code';

    /**
     * The input argument key for the column name to use.
     *
     * @var string
     */
    const COLUMN = 'column';

    /**
     * The input argument key for the values to use.
     *
     * @var string
     */
    const VALUES = 'values';

    /**
     * Query whether or not the passed input argument is valid.
     *
     * @param string $inputArgument The input argument to query for
     *
     * @return boolean TRUE if the input argument is valid, else FALSE
     */
    public function isInputArgument($inputArgument);
}
