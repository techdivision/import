<?php

/**
 * TechDivision\Import\Repositories\CustomerGroupRepositoryInterface
 *
 * PHP version 7
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */

namespace TechDivision\Import\Repositories;

/**
 * The interface to be implemented by repositories that allow loading customer groups.
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */
interface CustomerGroupRepositoryInterface extends RepositoryInterface
{

    /**
     * Returns an array with the available customer groups and their code as keys.
     *
     * @return array The array with the customer groups
     */
    public function findAll();
}
