<?php

/**
 * TechDivision\Import\Observers\Attribute\SelectObserver
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

namespace TechDivision\Import\Observers\Attribute;

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
class SelectObserver extends AbstractAttributeImportObserver
{

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Observers\Attribute\AttributeImportObserverInterface::handle()
     */
    public function handle($value)
    {

        // try to load the attribute option value
        $eavAttributeOptionValue = $this->getEavAttributeOptionValueByOptionValueAndStoreId($value, $this->getRowStoreId());

        // return the option ID
        return $eavAttributeOptionValue[MemberNames::OPTION_ID];
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
