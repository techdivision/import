<?php

/**
 * TechDivision\Import\Utils\EntityStatus
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class containing the entity status.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EntityStatus
{

    /**
     * The key with the key to decide to update/create a row.
     *
     * @var string
     */
    const MEMBER_NAME = 'techdivision_import_utils_entityStatus_memberName';

    /**
     * The key for the row to be created.
     *
     * @var string
     */
    const STATUS_CREATE = 'create';

    /**
     * The key for the row to be updated.
     *
     * @var string
     */
    const STATUS_UPDATE = 'update';
}
