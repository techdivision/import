<?php

/**
 * TechDivision\Import\Serializers\AdditionalAttributeCsvSerializer
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Serializers;

use TechDivision\Import\Configuration\CsvConfigurationInterface;
use TechDivision\Import\Utils\FrontendInputTypes;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Serializer implementation that un-/serializes the additional product attribues found in the CSV file
 * in the row 'additional_attributes'.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeCsvSerializer extends AbstractCsvSerializer
{

    /**
     * The factory instance for the CSV value serializer.
     *
     * @var \TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface
     */
    private $valueCsvSerializerFactory;

    /**
     * The CSV value serializer instance.
     *
     * @var \TechDivision\Import\Serializers\SerializerInterface
     */
    private $valueCsvSerializer;

    /**
     * The entity type from the configuration.
     *
     * @var array
     */
    private $entityType;

    /**
     *  The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    private $configuration;

    /**
     * The convert processor instance.
     *
     * @var \TechDivision\Import\Services\ImportProcessorInterface
     */
    private $importProcessor;

    /**
     * Initialize the serializer with the passed CSV value serializer factory.
     *
     * @param \TechDivision\Import\ConfigurationInterface                                   $configuration             The configuration instance
     * @param \TechDivision\Import\Services\ImportProcessorInterface                        $importProcessor           The processor instance
     * @param \TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface $valueCsvSerializerFactory The CSV value serializer factory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ImportProcessorInterface $importProcessor,
        ConfigurationAwareSerializerFactoryInterface $valueCsvSerializerFactory
    ) {

        // set the passed instances
        $this->configuration = $configuration;
        $this->importProcessor = $importProcessor;
        $this->valueCsvSerializerFactory = $valueCsvSerializerFactory;

        // load the entity type for the entity type defined in the configuration
        $this->entityType = $importProcessor->getEavEntityTypeByEntityTypeCode($configuration->getEntityTypeCode());
    }

    /**
     * Returns the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the factory instance for the CSV value serializer.
     *
     * @return \TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface The CSV value serializer factory instance
     */
    protected function getValueCsvSerializerFactory()
    {
        return $this->valueCsvSerializerFactory;
    }

    /**
     * Returns the CSV value serializer instance.
     *
     * @param \TechDivision\Import\Serializers\SerializerInterface $valueCsvSerializer The CSV value serializer instance
     *
     * @return void
     */
    protected function setValueCsvSerializer(SerializerInterface $valueCsvSerializer)
    {
        $this->valueCsvSerializer = $valueCsvSerializer;
    }

    /**
     * Returns the CSV value serializer instance.
     *
     * @return \TechDivision\Import\Serializers\SerializerInterface The CSV value serializer instance
     */
    protected function getValueCsvSerializer()
    {
        return $this->valueCsvSerializer;
    }

    /**
     * Returns the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    protected function getImportProcessor()
    {
        return $this->importProcessor;
    }

    /**
     * Returns entity type ID mapped from the configuration.
     *
     * @return integer The mapped entity type ID
     */
    protected function getEntityTypeId()
    {
        return $this->entityType[MemberNames::ENTITY_TYPE_ID];
    }

    /**
     * Returns the multiple value delimiter from the configuration.
     *
     * @return string The multiple value delimiter
     */
    protected function getMultipleValueDelimiter()
    {
        return $this->getConfiguration()->getMultipleValueDelimiter();
    }

    /**
     * Returns the multiple field delimiter from the configuration.
     *
     * @return string The multiple field delimiter
     */
    protected function getMultipleFieldDelimiter()
    {
        return $this->getConfiguration()->getMultipleFieldDelimiter();
    }

    /**
     * Loads and returns the attribute with the passed code from the database.
     *
     * @param string $attributeCode The code of the attribute to return
     *
     * @return array The attribute
     */
    protected function loadAttributeByAttributeCode($attributeCode)
    {
        return $this->getImportProcessor()->getEavAttributeByEntityTypeIdAndAttributeCode($this->getEntityTypeId(), $attributeCode);
    }

    /**
     * Packs the passed value according to the frontend input type of the attribute with the passed code.
     *
     * @param string $attributeCode The code of the attribute to pack the passed value for
     * @param mixed  $value         The value to pack
     *
     * @return string The packed value
     */
    protected function pack($attributeCode, $value)
    {

        // load the attibute with the passed code
        $attribute = $this->loadAttributeByAttributeCode($attributeCode);

        // pack the value according to the attribute's frontend input type
        switch ($attribute[MemberNames::FRONTEND_INPUT]) {
            case FrontendInputTypes::MULTISELECT:
                return implode($this->getMultipleValueDelimiter(), $value);
                break;

            case FrontendInputTypes::BOOLEAN:
                return $value === true ? 'true' : 'false';
                break;

            default:
                return $value;
        }
    }

    /**
     * Unpacks the passed value according to the frontend input type of the attribute with the passed code.
     *
     * @param string $attributeCode The code of the attribute to pack the passed value for
     * @param string $value         The value to unpack
     *
     * @return mixed The unpacked value
     */
    protected function unpack($attributeCode, $value)
    {

        // load the attibute with the passed code
        $attribute = $this->loadAttributeByAttributeCode($attributeCode);

        // unpack the value according to the attribute's frontend input type
        switch ($attribute[MemberNames::FRONTEND_INPUT]) {
            case FrontendInputTypes::MULTISELECT:
                return explode($this->getMultipleValueDelimiter(), $value);
                break;

            case FrontendInputTypes::BOOLEAN:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;

            default:
                return $value;
        }
    }

    /**
     * Passes the configuration and initializes the serializer.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The CSV configuration
     *
     * @return void
     */
    public function init(CsvConfigurationInterface $configuration)
    {

        // pass the configuration to the parent instance
        parent::init($configuration);

        // create the CSV value serializer instance
        $this->setValueCsvSerializer($this->getValueCsvSerializerFactory()->createSerializer($configuration));
    }

    /**
     * Unserializes the elements of the passed string.
     *
     * @param string|null $serialized The value to unserialize
     * @param string|null $delimiter  The delimiter used to unserialize the elements
     *
     * @return array The unserialized values
     * @see \TechDivision\Import\Serializers\SerializerInterface::unserialize()
     */
    public function unserialize($serialized = null, $delimiter = null)
    {

        // initialize the array for the attributes
        $attrs = array();

        // explode the additional attributes
        $additionalAttributes = $this->getValueCsvSerializer()->unserialize($serialized, $delimiter ? $delimiter : $this->getMultipleFieldDelimiter());

        // iterate over the attributes and append them to the row
        if (is_array($additionalAttributes)) {
            foreach ($additionalAttributes as $additionalAttribute) {
                // explode attribute code/option value from the attribute
                list ($attributeCode, $optionValue) = $this->explode($additionalAttribute, '=');
                $attrs[$attributeCode] = $this->unpack($attributeCode, $optionValue);
            }
        }

        // return the extracted array with the additional attributes
        return $attrs;
    }

    /**
     * Serializes the elements of the passed array.
     *
     * @param array|null  $unserialized The serialized data
     * @param string|null $delimiter    The delimiter used to serialize the values
     *
     * @return string The serialized array
     * @see \TechDivision\Import\Serializers\SerializerInterface::serialize()
     */
    public function serialize(array $unserialized = null, $delimiter = null)
    {

        // initialize the array for the attributes
        $attrs = array();

        // iterate over the attributes and append them to the row
        if (is_array($unserialized)) {
            foreach ($unserialized as $attributeCode => $optionValue) {
                $attrs[] = sprintf('%s=%s', $attributeCode, $this->pack($attributeCode, $optionValue));
            }
        }

        // implode the array with the packed additional attributes and return it
        return $this->getValueCsvSerializer()->serialize($attrs, $delimiter ? $delimiter : $this->getMultipleFieldDelimiter());
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string|null $value     The value to extract
     * @param string|null $delimiter The delimiter used to extrace the elements
     *
     * @return array|null The exploded values
     * @see \TechDivision\Import\Serializers\SerializerInterface::unserialize()
     */
    public function explode($value = null, $delimiter = null)
    {
        return $this->getValueCsvSerializer()->explode($value, $delimiter ? $delimiter : $this->getMultipleFieldDelimiter());
    }

    /**
     * Compacts the elements of the passed value by imploding them
     * with the also passed delimiter.
     *
     * @param array|null  $value     The values to compact
     * @param string|null $delimiter The delimiter use to implode the values
     *
     * @return string|null The compatected value
     * @see \TechDivision\Import\Serializers\SerializerInterface::serialize()
     */
    public function implode(array $value = null, $delimiter = null)
    {
        return $this->getValueCsvSerializer()->implode($value, $delimiter ? $delimiter : $this->getMultipleFieldDelimiter());
    }
}
