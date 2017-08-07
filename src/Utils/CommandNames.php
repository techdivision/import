<?php

/**
 * TechDivision\Import\Utils\CommandNames
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
 * Utility class containing the available command names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CommandNames extends \ArrayObject
{

    /**
     * The command name for the attribute import.
     *
     * @var string
     */
    const IMPORT_ATTRIBUTES = 'import:attributes';

    /**
     * The command name for the category import.
     *
     * @var string
     */
    const IMPORT_CATEGORIES = 'import:categories';

    /**
     * The command name for the product import.
     *
     * @var string
     */
    const IMPORT_PRODUCTS = 'import:products';

    /**
     * The command name for the product inventory import.
     *
     * @var string
     */
    const IMPORT_PRODUCTS_INVENTORY = 'import:products:inventory';

    /**
     * The command name for the product price import.
     *
     * @var string
     */
    const IMPORT_PRODUCTS_PRICE = 'import:products:price';

    /**
     * The command name for the command that creates an OK file.
     *
     * @var string
     */
    const IMPORT_CREATE_OK_FILE = 'import:create:ok-file';

    /**
     * The command name for the command that creates an configuration file.
     *
     * @var string
     */
    const IMPORT_CREATE_CONFIGURATION_FILE = 'import:create:configuration-file';

    /**
     * The command name for the command that clears the PID file.
     *
     * @var string
     */
    const IMPORT_CLEAR_PID_FILE = 'import:clear:pid-file';

    /**
     * Construct a new command names instance.
     *
     * @param array $commandNames The array with the additional command names
     * @link http://www.php.net/manual/en/arrayobject.construct.php
     */
    public function __construct(array $commandNames = array())
    {

        // merge the command names with the passed ones
        $mergedCommandNames = array_merge(
            array(
                CommandNames::IMPORT_ATTRIBUTES,
                CommandNames::IMPORT_CATEGORIES,
                CommandNames::IMPORT_PRODUCTS,
                CommandNames::IMPORT_PRODUCTS_PRICE,
                CommandNames::IMPORT_PRODUCTS_INVENTORY,
                CommandNames::IMPORT_CREATE_OK_FILE,
                CommandNames::IMPORT_CREATE_CONFIGURATION_FILE,
                CommandNames::IMPORT_CLEAR_PID_FILE
            ),
            $commandNames
        );

        // initialize the parent class with the merged command names
        parent::__construct($mergedCommandNames);
    }

    /**
     * Query whether or not the passed command name is valid.
     *
     * @param string $commandName The command name to query for
     *
     * @return boolean TRUE if the command name is valid, else FALSE
     */
    public function isCommandName($commandName)
    {
        return isset($this[$commandName]);
    }
}
