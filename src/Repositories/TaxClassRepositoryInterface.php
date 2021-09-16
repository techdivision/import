<?php

/**
 * TechDivision\Import\Repositories\TaxClassRepositoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

/**
 * Interface for a tax class data repository implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface TaxClassRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with the available tax classes and their
     * class names as keys.
     *
     * @return array The array with all available tax classes
     */
    public function findAll();
}
