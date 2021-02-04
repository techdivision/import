<?php

/**
 * TechDivision\Import\Actions\GenericDynamicIdentifierAction
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Actions;

use TechDivision\Import\Utils\PrimaryKeyUtilInterface;
use TechDivision\Import\Actions\Processors\ProcessorInterface;

/**
 * An action implementation that provides CRUD functionality and returns the ID of
 * the persisted entity for the `update` and `create` methods.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Dbal\Collection\Actions\GenericDynamicIdentifierAction
 */
class GenericDynamicIdentifierAction extends GenericIdentifierAction
{

    /**
     * Initialize the instance with the passed processors.
     *
     * @param \TechDivision\Import\Utils\PrimaryKeyUtilInterface              $primaryKeyUtil  The primary key utility instance
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $createProcessor The create processor instance
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $updateProcessor The update processor instance
     * @param \TechDivision\Import\Actions\Processors\ProcessorInterface|null $deleteProcessor The delete processor instance
     */
    public function __construct(
        PrimaryKeyUtilInterface $primaryKeyUtil,
        ProcessorInterface $createProcessor = null,
        ProcessorInterface $updateProcessor = null,
        ProcessorInterface $deleteProcessor = null
    ) {

        // pass the processor instances and the primary key name to the parent constructor
        parent::__construct($createProcessor, $updateProcessor, $deleteProcessor, $primaryKeyUtil->getPrimaryKeyMemberName());
    }
}
