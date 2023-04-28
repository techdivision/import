<?php

/**
 * TechDivision\Import\Observers\AttributeObserverTrait
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\OperationNames;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\ConfigurationKeys;

/**
 * Observer that creates/updates the EAV attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait AttributeObserverTrait
{

    /**
     * The trait that provides empty columns functionality.
     *
     * @var \TechDivision\Import\Observers\CleanUpEmptyColumnsTrait
     */
    use CleanUpEmptyColumnsTrait;

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
     * The attribute value in process.
     *
     * @var mixed
     */
    protected $attributeValue;

    /**
     * The attribute we're actually processing.
     *
     * @var array
     */
    protected $attribute;

    /**
     * The entity's existing attribues.
     *
     * @var array
     */
    protected $attributes;

    /**
     * The operation that has to be executed to update the attribute.
     *
     * @var string
     */
    protected $operation;

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
                        'Attributes for "%s" "%s" + store view code "%s" has already been processed',
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

        //Get data from config
        $ignoredAttributeValues = $this->getSubject()->getConfiguration()->getConfiguration()->getIgnoreAttributeValue();
        $entityTypeCode = $this->getSubject()->getConfiguration()->getConfiguration()->getEntityTypeCode();

        // Check if all attribute for this entity_type has to be ignored
        $isAllAttributeIgnored = isset($ignoredAttributeValues[$entityTypeCode]) && empty($ignoredAttributeValues[$entityTypeCode]);

        // iterate over the attributes and append them to the row
        foreach ($row as $key => $attributeValue) {
            // query whether or not attribute with the found code exists
            if (!isset($attributes[$attributeCode = $headers[$key]])) {
                // log a message in debug mode
                if ($this->isDebugMode()) {
                    $this->getSystemLogger()->debug(
                        $this->appendExceptionSuffix(
                            sprintf(
                                'Can\'t find attribute with attribute code "%s"',
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
                                'Found attribute with attribute code "%s"',
                                $attributeCode
                            )
                        )
                    );
                }
            }

            // if yes, load the attribute by its code
            $this->attribute = $attributes[$attributeCode];

            // load the backend type => to find the apropriate entity
            $backendType = $this->attribute[MemberNames::BACKEND_TYPE];
            if ($backendType === null) {
                // log a message in debug mode
                $this->getSystemLogger()->warning(
                    $this->appendExceptionSuffix(
                        sprintf(
                            'Found EMTPY backend type for attribute "%s"',
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

            // do nothing in non-default stores with global attributes
            if (isset($this->attribute[MemberNames::IS_GLOBAL]) &&
                $this->attribute[MemberNames::IS_GLOBAL] === 1 &&
                $storeId !== 0) {
                continue;
            }

            // query whether or not we've found a supported backend type
            if (isset($backendTypes[$backendType])) {
                // initialize attribute ID/code and backend type
                $this->backendType = $backendType;
                $this->attributeCode = $attributeCode;
                $this->attributeId = $this->attribute[MemberNames::ATTRIBUTE_ID];

                // set the attribute value as well as the original attribute value
                $this->attributeValue = $attributeValue;

                // initialize the persist method for the found backend type
                list ($persistMethod, , $deleteMethod) = $backendTypes[$backendType];

                // prepare the attribute vale and query whether or not it has to be persisted
                if ($this->hasChanges($value = $this->initializeAttribute($this->prepareAttributes()))) {
                    // query whether or not the entity's value has to be persisted or deleted. if the value is
                    // an empty string and the status is UPDATE, then the value exists and has to be deleted
                    // We need to user $attributeValue instead of $value[MemberNames::VALUE] in cases where
                    // value was casted by attribute type. E.g. special_price = 0 if value is empty string in CSV
                    switch ($this->operation) {
                        // create/update the attribute
                        case OperationNames::CREATE:
                            if (!$this->isValidateVarcharLength()) {
                                break;
                            }
                            $this->$persistMethod($value);
                            break;
                        case OperationNames::UPDATE:
                            if ($isAllAttributeIgnored ||
                                (isset($ignoredAttributeValues[$entityTypeCode]) && in_array($attributeCode, $ignoredAttributeValues[$entityTypeCode]))
                            ) {
                                $this->getSystemLogger()->debug(
                                    $this->appendExceptionSuffix(
                                        sprintf(
                                            'Ignore attribute "%s" on update with value "%s"',
                                            $attributeCode,
                                            $value['value']
                                        )
                                    )
                                );
                            } else {
                                if (!$this->isValidateVarcharLength()) {
                                    break;
                                }
                                $this->$persistMethod($value);
                            }
                            break;
                        // delete the attribute
                        case OperationNames::DELETE:
                            $this->$deleteMethod(array(MemberNames::VALUE_ID => $value[MemberNames::VALUE_ID]));
                            break;
                        // skip the attribute
                        case OperationNames::SKIP:
                            $this->getSubject()->getSystemLogger()->debug(sprintf('Skipped processing attribute "%s"', $attributeCode));
                            break;
                        // should never happen
                        default:
                            $this->getSubject()->getSystemLogger()->debug(sprintf('Found invalid entity status "%s" for attribute "%s"', $value[MemberNames::VALUE] ?? 'NULL', $attributeCode));
                    }
                } else {
                    $this->getSubject()->getSystemLogger()->debug(sprintf('Skip to persist value for attribute "%s"', $attributeCode));
                }

                // continue with the next value
                continue;
            }

            // log the debug message
            $this->getSystemLogger()->debug(
                $this->getSubject()->appendExceptionSuffix(
                    sprintf(
                        'Found invalid backend type %s for attribute "%s"',
                        $backendType,
                        $attributeCode
                    )
                )
            );
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function isValidateVarcharLength()
    {
        if ($this->backendType == "varchar" && mb_strlen($this->attributeValue) > 255) {
            // $this->attributeValue = substr($attributeValue, 0, 255);
            $message = sprintf('Skipped attribute "%s" cause value more then 255 signs. Detail: "%s"', $this->attributeCode, $this->attributeValue);
            $this->getSystemLogger()->error($this->getSubject()->appendExceptionSuffix($message));
            if (!$this->getSubject()->isStrictMode()) {
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    $this->attributeCode =>  $message
                                )
                            )
                        )
                    )
                );
            }
            return false;
        }
        return true;
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array|null The prepared attributes
     */
    protected function prepareAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getPrimaryKey();

        // load the store ID, use the admin store if NO store view code has been set
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // prepare the attribute values
        return $this->initializeEntity(
            $this->loadRawEntity(
                array(
                   $this->getPrimaryKeyMemberName() => $lastEntityId,
                    MemberNames::ATTRIBUTE_ID       => $this->attributeId,
                    MemberNames::STORE_ID           => $storeId
                )
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
     * Load's and return's a raw customer entity without primary key but the mandatory members only and nulled values.
     *
     * @param array $data An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    protected function loadRawEntity(array $data = array())
    {

        // laod the callbacks for the actual attribute code
        $callbacks = $this->getCallbacksByType($this->attributeCode);

        // invoke the pre-cast callbacks
        foreach ($callbacks as $callback) {
            $this->attributeValue = $callback->handle($this);
        }

        // load the default value
        $defaultValue = isset($this->attribute[MemberNames::DEFAULT_VALUE]) ? $this->attribute[MemberNames::DEFAULT_VALUE] : '';

        // load the value that has to be casted
        $value = $this->attributeValue === '' || $this->attributeValue === null ? $defaultValue : $this->attributeValue;

        // cast the value
        $castedValue = $this->castValueByBackendType($this->backendType, $value);

        // merge the casted value into the passed data and return it
        return array_merge(array(MemberNames::VALUE => $castedValue), $data);
    }

    /**
     * Initialize's and return's a new entity with the status 'create'.
     *
     * @param array $attr The attributes to merge into the new entity
     *
     * @return array The initialized entity
     */
    protected function initializeEntity(array $attr = array())
    {

        // initialize the operation name
        $this->operation = OperationNames::CREATE;

        // query whether or not the colunm IS empty and it is NOT in the
        // array with the default column values, because in that case we
        // want to skip processing the attribute
        if (array_key_exists($this->attributeCode, $this->getDefaultColumnValues()) === false && ($this->attributeValue === '' || $this->attributeValue == null) && !$this->attribute['is_required']) {
            $this->operation = OperationNames::SKIP;
        }

        // initialize the entity with the passed data
        return parent::initializeEntity($attr);
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity        The entity to merge the attributes into
     * @param array       $attr          The attributes to be merged
     * @param string|null $changeSetName The change set name to use
     *
     * @return array The merged entity
     */
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null)
    {

        // we want to update the attribute, if we're here
        $this->operation = OperationNames::UPDATE;

        // query whether or not the column is EMPTY
        if ($this->attributeValue === '' || $this->attributeValue === null) {
            // if the value is empty AND it is IN the array with default column values
            // BUT it is NOT in the array with columns we want to clean-up the default
            // column value has to be removed, because we do NOT want to override the
            // value existing in the database
            if (array_key_exists($this->attributeCode, $this->getDefaultColumnValues()) &&
                array_key_exists($this->attributeCode, $this->getCleanUpEmptyColumnKeys()) === false
            ) {
                // remove the value from the array with the column values, because
                // this is the default value from the database and it should NOT
                // override the value from the entity in that case
                unset($attr[MemberNames::VALUE]);
            } else {
                // otherwise keep the value and DELETE the whole attribute from
                // the database
                $this->operation = OperationNames::DELETE;
            }
        }

        // merge and return the data
        return parent::mergeEntity($entity, $attr, $changeSetName);
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

    /**
     * Query whether or not the entity has to be processed.
     *
     * @param array $entity The entity to query for
     *
     * @return boolean TRUE if the entity has to be processed, else FALSE
     */
    abstract protected function hasChanges(array $entity);

    /**
     * Query whether or not a value for the column with the passed name exists.
     *
     * @param string $name The column name to query for a valid value
     *
     * @return boolean TRUE if the value is set, else FALSE
     */
    abstract protected function hasValue($name);
}
