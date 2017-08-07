<?php

/**
 * TechDivision\Import\Subjects\AbstractSubject
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\EntityTypeCodes;

/**
 * An abstract EAV subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractEavSubject extends AbstractSubject implements EavSubjectInterface
{

    /**
     * The available EAV attributes, grouped by their attribute set and the attribute set name as keys.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The available user defined EAV attributes, grouped by their entity type.
     *
     * @var array
     */
    protected $userDefinedAttributes = array();

    /**
     * The attribute set of the entity that has to be created.
     *
     * @var array
     */
    protected $attributeSet = array();

    /**
     * The available EAV attribute sets.
     *
     * @var array
     */
    protected $attributeSets = array();

    /**
     * The mapping for the supported backend types (for the EAV entity) => persist methods.
     *
     * @var array
     */
    protected $backendTypes = array(
        BackendTypeKeys::BACKEND_TYPE_DATETIME => array('persistDatetimeAttribute', 'loadDatetimeAttribute'),
        BackendTypeKeys::BACKEND_TYPE_DECIMAL  => array('persistDecimalAttribute', 'loadDecimalAttribute'),
        BackendTypeKeys::BACKEND_TYPE_INT      => array('persistIntAttribute', 'loadIntAttribute'),
        BackendTypeKeys::BACKEND_TYPE_TEXT     => array('persistTextAttribute', 'loadTextAttribute'),
        BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
    );

    /**
     * The mappings for the entity type code to attribute set.
     *
     * @var array
     */
    protected $entityTypeCodeToAttributeSetMappings = array(
        EntityTypeCodes::CATALOG_PRODUCT           => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_PRICE     => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY => EntityTypeCodes::CATALOG_PRODUCT,
        EntityTypeCodes::CATALOG_CATEGORY          => EntityTypeCodes::CATALOG_CATEGORY,
        EntityTypeCodes::EAV_ATTRIBUTE             => EntityTypeCodes::EAV_ATTRIBUTE,
        EntityTypeCodes::NONE                      => EntityTypeCodes::NONE
    );

    /**
     * The default mappings for the user defined attributes, based on the attributes frontend input type.
     *
     * @var array
     */
    protected $defaultFrontendInputCallbackMappings = array();

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings()
    {
        return $this->defaultFrontendInputCallbackMappings;
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($serial);

        // load the global data we've prepared initially
        $this->attributes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::EAV_ATTRIBUTES];
        $this->attributeSets = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ATTRIBUTE_SETS];
        $this->userDefinedAttributes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::EAV_USER_DEFINED_ATTRIBUTES];

        // load the default frontend callback mappings from the child instance
        $defaultFrontendInputCallbackMappings = $this->getDefaultFrontendInputCallbackMappings();

        // load the user defined attributes and add the callback mappings
        foreach ($this->getEavUserDefinedAttributes() as $eavAttribute) {
            // load attribute code and frontend input type
            $attributeCode = $eavAttribute[MemberNames::ATTRIBUTE_CODE];
            $frontendInput = $eavAttribute[MemberNames::FRONTEND_INPUT];

            // query whether or not the array for the mappings has been initialized
            if (!isset($this->callbackMappings[$attributeCode])) {
                $this->callbackMappings[$attributeCode] = array();
            }

            // set the appropriate callback mapping for the attributes input type
            if (isset($defaultFrontendInputCallbackMappings[$frontendInput])) {
                $this->callbackMappings[$attributeCode][] = $defaultFrontendInputCallbackMappings[$frontendInput];
            }
        }

        // prepare the callbacks
        parent::setUp($serial);
    }

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    public function getBackendTypes()
    {
        return $this->backendTypes;
    }

    /**
     * Set's the attribute set of the product that has to be created.
     *
     * @param array $attributeSet The attribute set
     *
     * @return void
     */
    public function setAttributeSet(array $attributeSet)
    {
        $this->attributeSet = $attributeSet;
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    public function getAttributeSet()
    {
        return $this->attributeSet;
    }

    /**
     * Cast's the passed value based on the backend type information.
     *
     * @param string $backendType The backend type to cast to
     * @param mixed  $value       The value to be casted
     *
     * @return mixed The casted value
     */
    public function castValueByBackendType($backendType, $value)
    {

        // cast the value to a valid timestamp
        if ($backendType === BackendTypeKeys::BACKEND_TYPE_DATETIME) {
            return \DateTime::createFromFormat($this->getSourceDateFormat(), $value)->format('Y-m-d H:i:s');
        }

        // cast the value to a float value
        if ($backendType === BackendTypeKeys::BACKEND_TYPE_FLOAT) {
            return (float) $value;
        }

        // cast the value to an integer
        if ($backendType === BackendTypeKeys::BACKEND_TYPE_INT) {
            return (int) $value;
        }

        // we don't need to cast strings
        return $value;
    }

    /**
     * Return's the entity type code to be used.
     *
     * @return string The entity type code to be used
     */
    public function getEntityTypeCode()
    {

        // load the entity type code from the configuration
        $entityTypeCode = $this->getConfiguration()->getConfiguration()->getEntityTypeCode();

        // try to map the entity type code
        if (isset($this->entityTypeCodeToAttributeSetMappings[$entityTypeCode])) {
            $entityTypeCode = $this->entityTypeCodeToAttributeSetMappings[$entityTypeCode];
        }

        // return the (mapped) entity type code
        return $entityTypeCode;
    }

    /**
     * Return's the attribute set with the passed attribute set name.
     *
     * @param string $attributeSetName The name of the requested attribute set
     *
     * @return array The attribute set data
     * @throws \Exception Is thrown, if the attribute set or the given entity type with the passed name is not available
     */
    public function getAttributeSetByAttributeSetName($attributeSetName)
    {

        // query whether or not attribute sets for the actualy entity type code are available
        if (isset($this->attributeSets[$entityTypeCode = $this->getEntityTypeCode()])) {
            // load the attribute sets for the actualy entity type code
            $attributSets = $this->attributeSets[$entityTypeCode];

            // query whether or not, the requested attribute set is available
            if (isset($attributSets[$attributeSetName])) {
                return $attributSets[$attributeSetName];
            }

            // throw an exception, if not
            throw new \Exception(
                $this->appendExceptionSuffix(
                    sprintf('Found invalid attribute set name "%s"', $attributeSetName)
                )
            );
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid entity type code "%s"', $entityTypeCode)
            )
        );
    }

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception Is thrown, if the attribute set or the given entity type with the passed name is not available
     */
    public function getAttributes()
    {

        // query whether or not, the requested EAV attributes are available
        if (isset($this->attributes[$entityTypeCode = $this->getEntityTypeCode()])) {
            // load the attributes for the entity type code
            $attributes = $this->attributes[$entityTypeCode];

            // query whether or not attributes for the actual attribute set name
            if (isset($attributes[$attributeSetName = $this->attributeSet[MemberNames::ATTRIBUTE_SET_NAME]])) {
                return $attributes[$attributeSetName];
            }

            // throw an exception, if not
            throw new \Exception(
                $this->appendExceptionSuffix(
                    sprintf('Found invalid attribute set name "%s"', $attributeSetName)
                )
            );
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid entity type code "%s"', $entityTypeCode)
            )
        );
    }

    /**
     * Return's an array with the available user defined EAV attributes for the actual entity type.
     *
     * @return array The array with the user defined EAV attributes
     */
    public function getEavUserDefinedAttributes()
    {

        // initialize the array with the user defined EAV attributes
        $eavUserDefinedAttributes = array();

        // query whether or not user defined EAV attributes for the actualy entity type are available
        if (isset($this->userDefinedAttributes[$entityTypeCode = $this->getEntityTypeCode()])) {
            $eavUserDefinedAttributes = $this->userDefinedAttributes[$entityTypeCode];
        }

        // return the array with the user defined EAV attributes
        return $eavUserDefinedAttributes;
    }

    /**
     * Return's the EAV attribute with the passed attribute code.
     *
     * @param string $attributeCode The attribute code
     *
     * @return array The array with the EAV attribute
     * @throws \Exception Is thrown if the attribute with the passed code is not available
     */
    public function getEavAttributeByAttributeCode($attributeCode)
    {

        // load the attributes
        $attributes = $this->getAttributes();

        // query whether or not the attribute exists
        if (isset($attributes[$attributeCode])) {
            return $attributes[$attributeCode];
        }

        // throw an exception if the requested attribute is not available
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Can\'t load attribute with code "%s"', $attributeCode)
            )
        );
    }
}
