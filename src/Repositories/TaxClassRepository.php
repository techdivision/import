<?php

/**
 * TechDivision\Import\Repositories\TaxClassRepository
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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class TaxClassRepository extends AbstractRepository
{

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->taxClassesStmt = $this->getConnection()->prepare($utilityClassName::TAX_CLASSES);
    }

    /**
     * Return's an array with the available tax classes and their
     * class names as keys.
     *
     * @return array The array with all available tax classes
     */
    public function findAll()
    {

        // initialize the array with the available tax classes
        $taxClasses = array();

        // execute the prepared statement
        $this->taxClassesStmt->execute();

        // fetch the tax classes and assemble them as array with the class name as key
        foreach ($this->taxClassesStmt->fetchAll() as $taxClass) {
            $taxClasses[$taxClass[MemberNames::CLASS_NAME]] = $taxClass;
        }

        // return the array with the tax classes
        return $taxClasses;
    }
}
