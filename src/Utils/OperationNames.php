<?php

/**
 * TechDivision\Import\Utils\OperationNames
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
 * Utility class containing the operation names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
