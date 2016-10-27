<?php

/**
 * TechDivision\Import\Observers\Product\ProductAttributeObserver
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

namespace TechDivision\Import\Observers\Product;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductAttributeObserver extends AbstractProductImportObserver
{

    /**
     * The ID of the attribute to create the values for.
     *
     * @var integer
     */
    protected $attributeId;

    /**
     * The attribute code of the attribute to create the values for.
     *
     * @var string
     */
    protected $attributeCode;

    /**
     * The backend type of the attribute to create the values for.
     *
     * @var string
     */
    protected $backendType;

    /**
     * The the persist method for the found backend type.
     *
     * @var string
     */
    protected $persistMethod;

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // initialize the store view code
        $this->setStoreViewCode($row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: 'admin');

        // load the attributes by the found attribute set
        $attributes = $this->getAttributes();

        // iterate over the attribute related by the found attribute set
        foreach ($attributes as $attribute) {
            // load the attribute code/ID
            $attributeCode = $attribute[MemberNames::ATTRIBUTE_CODE];
            $attributeId = (integer) $attribute[MemberNames::ATTRIBUTE_ID];

            // query weather or not we've a mapping, if yes, map the attribute code
            $attributeCode = $this->mapAttributeCodeByHeaderMapping($attributeCode);

            // query whether or not we found the column in the CSV file
            if (!isset($headers[$attributeCode]) ||
                !isset($row[$headers[$attributeCode]])
            ) {
                continue;
            }

            // query whether or not, a value is available
            if ($row[$headers[$attributeCode]] === null ||
                trim($row[$headers[$attributeCode]]) === ''
            ) {
                continue;
            }

            // load the backend type => to find the apropriate entity
            $backendType = $attribute[MemberNames::BACKEND_TYPE];
            if ($backendType == null) {
                $this->getSystemLogger()->warning(
                    sprintf('Found EMTPY backend type for attribute %s', $attributeCode)
                );
                continue;
            }

            // load the supported backend types
            $backendTypes = $this->getBackendTypes();

            // query whether or not we've found a supported backend type
            if (isset($backendTypes[$backendType])) {
                // initialize the persist method for the found backend type
                $this->setPersistMethod($backendTypes[$backendType]);

                // initialize attribute ID/code and backend type
                $this->setAttributeId($attributeId);
                $this->setAttributeCode($attributeCode);
                $this->setBackendType($backendType);

                // load the row's value and persist it
                $this->processAttribute($row[$headers[$attributeCode]]);

            } else {
                $this->getSystemLogger()->debug(
                    sprintf('Found invalid backend type %s for attribute %s', $backendType, $attributeCode)
                );
            }
        }

        // returns the row
        return $row;
    }

    /**
     * This method finally persists the passed value by invoking the
     * persist method defined by the attribute's backend type.
     *
     * @param mixed $value The value to persist
     *
     * @return void
     */
    public function processAttribute($value)
    {

        // laod the callbacks for the actual attribute code
        $preCastCallbacks = $this->getPreCastCallbacksByAttributeCode($this->getAttributeCode());

        // invoke the pre-cast callbacks
        foreach ($preCastCallbacks as $listener) {
            $value = $listener->handle($value);
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // load the ID of the attribute to create the values for
        $attributeId = $this->getAttributeId();

        // load the store ID
        $storeId = $this->getRowStoreId();

        // load the backend type of the actual attribute
        $backendType = $this->getBackendType();

        // cast the value based on the backend type
        $castedValue = $this->castValueByBackendType($backendType, $value);

        // prepare the attribute values
        $attribute = array($lastEntityId, $attributeId, $storeId, $castedValue);

        // initialize and persist the entity attribute
        $persistMethod = $this->getPersistMethod();
        $this->$persistMethod($attribute);
    }

    /**
     * Set's the persist method for the found backend type.
     *
     * @param string $persistMethod The persist method
     *
     * @return void
     */
    public function setPersistMethod($persistMethod)
    {
        $this->persistMethod = $persistMethod;
    }

    /**
     * Return's the persist method for the found backend type.
     *
     * @return string The persist method
     */
    public function getPersistMethod()
    {
        return $this->persistMethod;
    }

    /**
     * Set's the backend type of the attribute to create the values for.
     *
     * @param string $backendType The backend type
     *
     * @return void
     */
    public function setBackendType($backendType)
    {
        $this->backendType = $backendType;
    }

    /**
     * Return's the backend type of the attribute to create the values for.
     *
     * @return string The backend type
     */
    public function getBackendType()
    {
        return $this->backendType;
    }

    /**
     * Set's the attribute code of the attribute to create the values for.
     *
     * @param string $attributeCode The attribute code
     *
     * @return void
     */
    public function setAttributeCode($attributeCode)
    {
        $this->attributeCode = $attributeCode;
    }

    /**
     * Return's the attribute code of the attribute to create the values for.
     *
     * @return string The attribute code
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * Set's the ID of the attribute to create the values for.
     *
     * @param integer $attributeId The attribute ID
     *
     * @return void
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * Return's the ID of the attribute to create the values for.
     *
     * @return integer The attribute ID
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function setStoreViewCode($storeViewCode)
    {
        $this->getSubject()->setStoreViewCode($storeViewCode);
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    public function mapAttributeCodeByHeaderMapping($attributeCode)
    {
        return $this->getSubject()->mapAttributeCodeByHeaderMapping($attributeCode);
    }

    /**
     * Return's the array containing callbacks necessary to cast values found in CSV file.
     *
     * @param string $attributeCode The attribute code to return the callbacks for
     *
     * @return array The array with the callbacks
     */
    public function getPreCastCallbacksByAttributeCode($attributeCode)
    {
        return $this->getSubject()->getPreCastCallbacksByAttributeCode($attributeCode);
    }

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    public function getBackendTypes()
    {
        return $this->getSubject()->getBackendTypes();
    }

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception
     */
    public function getAttributes()
    {
        return $this->getSubject()->getAttributes();
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
     * Persist's the passed product varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute)
    {
        $this->getSubject()->persistProductVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute)
    {
        $this->getSubject()->persistProductIntAttribute($attribute);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute)
    {
        $this->getSubject()->persistProductDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute)
    {
        $this->getSubject()->persistProductDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute)
    {
        $this->getSubject()->persistProductTextAttribute($attribute);
    }
}
