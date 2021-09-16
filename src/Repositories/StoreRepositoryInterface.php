<?php

/**
 * TechDivision\Import\Repositories\StoreRepositoryInterface
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
 * Interface for store repository implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface StoreRepositoryInterface extends RepositoryInterface
{

    /**
     * Return's an array with the available stores and their
     * store codes as keys.
     *
     * @return array The array with all available stores
     */
    public function findAll();

    /**
     * Return's the default store.
     *
     * @return array The default store
     */
    public function findOneByDefault();
}
