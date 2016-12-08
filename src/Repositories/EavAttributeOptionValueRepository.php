<?php

/**
 * TechDivision\Import\Repositories\EavAttributeOptionValueRepository
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

/**
 * Repository implementation to load EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueRepository extends AbstractRepository
{

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

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
        $this->eavAttributeOptionValueStmt = $this->getConnection()->prepare($utilityClassName::EAV_ATTRIBUTE_OPTION_VALUE);
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance or FALSE if the value is NOT available
     */
    public function findEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {

        // query whether or not we've already loaded the value
        if (!isset($this->cache[$value][$storeId])) {
            // try to load the attribute option value
            $this->eavAttributeOptionValueStmt->execute(array($value, $storeId));
            $this->cache[$value][$storeId] = reset($this->eavAttributeOptionValueStmt->fetchAll());

        }

        // return the value from the cache
        return $this->cache[$value][$storeId];
    }
}
