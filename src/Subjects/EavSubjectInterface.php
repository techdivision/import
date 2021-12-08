<?php

/**
 * TechDivision\Import\Subjects\EavSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

/**
 * The interface for all EAV subject implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EavSubjectInterface extends CastValueSubjectInterface
{

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings();

    /**
     * Return's the entity type code to be used.
     *
     * @return string The entity type code to be used
     */
    public function getEntityTypeCode();

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId($default = null);

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    public function getBackendTypes();

    /**
     * Set's the attribute set of the product that has to be created.
     *
     * @param array $attributeSet The attribute set
     *
     * @return void
     */
    public function setAttributeSet(array $attributeSet);

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    public function getAttributeSet();

    /**
     * Return's the attribute set with the passed attribute set name.
     *
     * @param string $attributeSetName The name of the requested attribute set
     *
     * @return array The attribute set data
     * @throws \Exception Is thrown, if the attribute set with the passed name is not available
     */
    public function getAttributeSetByAttributeSetName($attributeSetName);

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception Is thrown if the attributes for the actual attribute set are not available
     */
    public function getAttributes();

    /**
     * Return's an array with the available user defined EAV attributes for the actual entity type.
     *
     * @return array The array with the user defined EAV attributes
     */
    public function getEavUserDefinedAttributes();

    /**
     * Return's the EAV attribute with the passed attribute code.
     *
     * @param string $attributeCode The attribute code
     *
     * @return array The array with the EAV attribute
     * @throws \Exception Is thrown if the attribute with the passed code is not available
     */
    public function getEavAttributeByAttributeCode($attributeCode);
}
