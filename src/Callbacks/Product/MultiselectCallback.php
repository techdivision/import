<?php

/**
 * TechDivision\Import\Callbacks\Product\MultiselectCallback
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Callbacks\Product;

use TechDivision\Import\Utils\MemberNames;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class MultiselectCallback extends AbstractProductImportCallback
{

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Callbacks\Product\ProductImportCallbackInterface::handle()
     */
    public function handle($value)
    {

        // explode the multiselect values
        $vals = explode('|', $value);

        // initialize the array for the mapped values
        $mappedValues = array();

        // convert the option values into option value ID's
        foreach ($vals as $val) {
            $eavAttributeOptionValue = $this->getEavAttributeOptionValueByOptionValueAndStoreId($val, $this->getRowStoreId());
            $mappedValues[] = $eavAttributeOptionValue[MemberNames::OPTION_ID];
        }

        // re-concatenate and return the values
        return implode(',', $mappedValues);
    }

    /**
     * Return's the store ID of the actual row.
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId()
    {
        return $this->getSubject()->getRowStoreId();
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getSubject()->getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }
}
