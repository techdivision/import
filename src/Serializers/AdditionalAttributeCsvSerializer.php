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
     * Initialize the serializer with the passed CSV value serializer factory.
     *
     * @param \TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface $valueCsvSerializerFactory The CSV value serializer factory
     */
    public function __construct(ConfigurationAwareSerializerFactoryInterface $valueCsvSerializerFactory)
    {
        $this->valueCsvSerializerFactory = $valueCsvSerializerFactory;
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
     *
     * @return array The unserialized values
     * @see \TechDivision\Import\Serializers\SerializerInterface::unserialize()
     */
    public function unserialize($serialized = null)
    {

        // initialize the array for the unserialized additional attributes
        $attributes = array();

        // explode the additional attributes
        if ($additionalAttributes = $this->explode($serialized)) {
            // iterate over the attributes and append them to the row
            foreach ($additionalAttributes as $additionalAttribute) {
                // explode attribute code/option value from the attribute
                list ($attributeCode, $optionValue) = explode('=', $additionalAttribute);

                // extract the key/value pairs into an array
                $attributes[$attributeCode] = $optionValue;
            }
        }

        // return the array with the unserialized additional attributes
        return $attributes;
    }

    /**
     * Serializes the elements of the passed array.
     *
     * @param array|null $unserialized The serialized data
     *
     * @return string The serialized array
     * @see \TechDivision\Import\Serializers\SerializerInterface::serialize()
     */
    public function serialize(array $unserialized = null)
    {

        // initialize the array
        $attributes = array();

        if (is_array($unserialized)) {
            // serialize the key/value pairs into the array
            foreach ($unserialized as $attributeCode => $attributeValue) {
                $attributes[] = implode('=', array($attributeCode, $attributeValue));
            }
        }

        // serialize the array itself
        return $this->implode($attributes);
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
        return $this->getValueCsvSerializer()->explode($value, $delimiter);
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
        return $this->getValueCsvSerializer()->implode($value, $delimiter);
    }
}
