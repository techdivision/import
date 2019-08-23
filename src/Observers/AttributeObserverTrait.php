<?php

/**
 * TechDivision\Import\Observers\AttributeObserverTrait
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\ConfigurationKeys;

/**
 * Observer that creates/updates the EAV attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait AttributeObserverTrait
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
     * The attribute value to process.
     *
     * @var mixed
     */
    protected $attributeValue;

    /**
     * The array with the column keys that has to be cleaned up when their values are empty.
     *
     * @var array
     */
    protected $cleanUpEmptyColumnKeys;

    /**
     * The entity's existing attribues.
     *
     * @var array
     */
    protected $attributes;

    /**
     * The attribute code that has to be processed.
     *
     * @return string The attribute code
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * The attribute value that has to be processed.
     *
     * @return string The attribute value
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * Remove all the empty values from the row and return the cleared row.
     *
     * @return array The cleared row
     */
    protected function clearRow()
    {

        // query whether or not the column keys has been initialized
        if ($this->cleanUpEmptyColumnKeys === null) {
            // initialize the array with the column keys that has to be cleaned-up
            $this->cleanUpEmptyColumnKeys = array();

            // query whether or not column names that has to be cleaned up have been configured
            if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)) {
                // if yes, load the column names
                $cleanUpEmptyColumns = $this->getSubject()->getCleanUpColumns();

                // translate the column names into column keys
                foreach ($cleanUpEmptyColumns as $cleanUpEmptyColumn) {
                    if ($this->hasHeader($cleanUpEmptyColumn)) {
                        $this->cleanUpEmptyColumnKeys[] = $this->getHeader($cleanUpEmptyColumn);
                    }
                }
            }
        }

        // remove all the empty values from the row, expected the columns has to be cleaned-up
        foreach ($this->row as $key => $value) {
            // query whether or not the value is empty AND the column has NOT to be cleaned-up
            if (($value === null || $value === '') && in_array($key, $this->cleanUpEmptyColumnKeys) === false) {
                unset($this->row[$key]);
            }
        }

        // finally return the clean row
        return $this->row;
    }

    /**
     * Returns the value(s) of the primary key column(s). As the primary key column can
     * also consist of two columns, the return value can be an array also.
     *
     * @return mixed The primary key value(s)
     */
    protected function getPrimaryKeyValue()
    {
        return $this->getValue($this->getPrimaryKeyColumnName());
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the store view code
        $this->prepareStoreViewCode();

        // load the store ID, use the admin store if NO store view code has been set
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // load the entity's existing attributes
        $this->getAttributesByPrimaryKeyAndStoreId($this->getPrimaryKey(), $storeId);

        // load the store view - if no store view has been set, we assume the admin
        // store view, which will contain the default (fallback) attribute values
        $storeViewCode = $this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN);

        // query whether or not the row has already been processed
        if ($this->storeViewHasBeenProcessed($pk = $this->getPrimaryKeyValue(), $storeViewCode)) {
            // log a message
            $this->getSystemLogger()->warning(
                $this->appendExceptionSuffix(
                    sprintf(
                        'Attributes for %s "%s" + store view code "%s" has already been processed',
                        $this->getPrimaryKeyColumnName(),
                        $pk,
                        $storeViewCode
                    )
                )
            );

            // return immediately
            return;
        }

        // load the attributes by the found attribute set and the backend types
        $attributes = $this->getAttributes();
        $backendTypes = $this->getBackendTypes();

        // load the header keys
        $headers = array_flip($this->getHeaders());

        // remove all the empty values from the row
        $row = $this->clearRow();

        // iterate over the attributes and append them to the row
        foreach ($row as $key => $attributeValue) {
            // query whether or not attribute with the found code exists
            if (!isset($attributes[$attributeCode = $headers[$key]])) {
                // log a message in debug mode
                if ($this->isDebugMode()) {
                    $this->getSystemLogger()->debug(
                        $this->appendExceptionSuffix(
                            sprintf(
                                'Can\'t find attribute with attribute code %s',
                                $attributeCode
                            )
                        )
                    );
                }

                // stop processing
                continue;
            } else {
                // log a message in debug mode
                if ($this->isDebugMode()) {
                // log a message in debug mode
                    $this->getSystemLogger()->debug(
                        $this->appendExceptionSuffix(
                            sprintf(
                                'Found attribute with attribute code %s',
                                $attributeCode
                            )
                        )
                    );
                }
            }

            // if yes, load the attribute by its code
            $attribute = $attributes[$attributeCode];

            // load the backend type => to find the apropriate entity
            $backendType = $attribute[MemberNames::BACKEND_TYPE];
            if ($backendType === null) {
                // log a message in debug mode
                $this->getSystemLogger()->warning(
                    $this->appendExceptionSuffix(
                        sprintf(
                            'Found EMTPY backend type for attribute %s',
                            $attributeCode
                        )
                    )
                );
                // stop processing
                continue;
            }

            // do nothing on static backend type
            if ($backendType === BackendTypeKeys::BACKEND_TYPE_STATIC) {
                continue;
            }

            // query whether or not we've found a supported backend type
            if (isset($backendTypes[$backendType])) {
                // initialize attribute ID/code and backend type
                $this->attributeId = $attribute[MemberNames::ATTRIBUTE_ID];
                $this->attributeCode = $attributeCode;
                $this->backendType = $backendType;

                // initialize the persist method for the found backend type
                list ($persistMethod, , $deleteMethod) = $backendTypes[$backendType];

                // set the attribute value
                $this->attributeValue = $attributeValue;

                // prepare/initialize the attribute value
                $value = $this->initializeAttribute($this->prepareAttributes());

                // query whether or not the entity's value has to be persisted or deleted. if the value is
                // an empty string and the status is UPDATE, then the value exists and has to be deleted
                // We need to user $attributeValue instead of $value[MemberNames::VALUE] in cases where
                // value was casted by attribute type. E.g. special_price = 0 if value is empty string in CSV
                if ($attributeValue === '' && $value[EntityStatus::MEMBER_NAME] === EntityStatus::STATUS_UPDATE) {
                    $this->$deleteMethod(array(MemberNames::VALUE_ID => $value[MemberNames::VALUE_ID]));
                } elseif ($attributeValue !== '' && $value[MemberNames::VALUE] !== null) {
                    $this->$persistMethod($value);
                } else {
                    // log a debug message, because this should never happen
                    $this->getSubject()->getSystemLogger()->debug(sprintf('Found empty value for attribute "%s"', $attributeCode));
                }

                // continue with the next value
                continue;
            }

            // log the debug message
            $this->getSystemLogger()->debug(
                $this->getSubject()->appendExceptionSuffix(
                    sprintf(
                        'Found invalid backend type %s for attribute %s',
                        $backendType,
                        $attributeCode
                    )
                )
            );
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array|null The prepared attributes
     */
    protected function prepareAttributes()
    {

        // laod the callbacks for the actual attribute code
        $callbacks = $this->getCallbacksByType($this->attributeCode);

        // invoke the pre-cast callbacks
        foreach ($callbacks as $callback) {
            $this->attributeValue = $callback->handle($this);
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getPrimaryKey();

        // load the store ID, use the admin store if NO store view code has been set
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // cast the value based on the backend type
        $castedValue = $this->castValueByBackendType($this->backendType, $this->attributeValue);

        // prepare the attribute values
        return $this->initializeEntity(
            array(
               $this->getPrimaryKeyMemberName() => $lastEntityId,
                MemberNames::ATTRIBUTE_ID       => $this->attributeId,
                MemberNames::STORE_ID           => $storeId,
                MemberNames::VALUE              => $castedValue
            )
        );
    }

    /**
     * Initialize the category product with the passed attributes and returns an instance.
     *
     * @param array $attr The category product attributes
     *
     * @return array The initialized category product
     */
    protected function initializeAttribute(array $attr)
    {
        return $attr;
    }

    /**
     * Return's the array with callbacks for the passed type.
     *
     * @param string $type The type of the callbacks to return
     *
     * @return array The callbacks
     */
    protected function getCallbacksByType($type)
    {
        return $this->getSubject()->getCallbacksByType($type);
    }

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    protected function getBackendTypes()
    {
        return $this->getSubject()->getBackendTypes();
    }

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception
     */
    protected function getAttributes()
    {
        return $this->getSubject()->getAttributes();
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    abstract protected function getAttributesByPrimaryKeyAndStoreId($pk, $storeId);

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    abstract protected function getSystemLogger($name = LoggerKeys::SYSTEM);

    /**
     * Return's the PK to create the product => attribute relation.
     *
     * @return integer The PK to create the relation with
     */
    abstract protected function getPrimaryKey();

    /**
     * Return's the PK column name to create the product => attribute relation.
     *
     * @return string The PK column name
     */
    abstract protected function getPrimaryKeyMemberName();

    /**
     * Return's the column name that contains the primary key.
     *
     * @return string the column name that contains the primary key
     */
    abstract protected function getPrimaryKeyColumnName();

    /**
     * Queries whether or not the passed PK and store view code has already been processed.
     *
     * @param string $pk            The PK to check been processed
     * @param string $storeViewCode The store view code to check been processed
     *
     * @return boolean TRUE if the PK and store view code has been processed, else FALSE
     */
    abstract protected function storeViewHasBeenProcessed($pk, $storeViewCode);

    /**
     * Persist's the passed varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistVarcharAttribute($attribute);

    /**
     * Persist's the passed integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistIntAttribute($attribute);

    /**
     * Persist's the passed decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistDecimalAttribute($attribute);

    /**
     * Persist's the passed datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistDatetimeAttribute($attribute);

    /**
     * Persist's the passed text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistTextAttribute($attribute);

    /**
     * Delete's the datetime attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    abstract protected function deleteDatetimeAttribute(array $row, $name = null);

    /**
     * Delete's the decimal attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    abstract protected function deleteDecimalAttribute(array $row, $name = null);

    /**
     * Delete's the integer attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    abstract protected function deleteIntAttribute(array $row, $name = null);

    /**
     * Delete's the text attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    abstract protected function deleteTextAttribute(array $row, $name = null);

    /**
     * Delete's the varchar attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    abstract protected function deleteVarcharAttribute(array $row, $name = null);
}
