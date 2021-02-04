<?php

/**
 * TechDivision\Import\Serializers\ValueCsvSerializer
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

namespace TechDivision\Import\Serializers;

/**
 * Serializer to serialize/unserialize simple column values.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Serializer\ValueCsvSerializer
 */
class ValueCsvSerializer extends AbstractCsvSerializer
{

    /**
     * The delimiter to override the one from the configuration with.
     *
     * @var string
     */
    private $delimiter;

    /**
     * The delimiter to use instead the one from the configuration.
     *
     * @param string $delimiter The delimiter
     *
     * @return void
     */
    protected function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Returns the delimiter to use instead the one from the configuration.
     *
     * @return string The delimiter
     */
    protected function getDelimiter()
    {
        return $this->delimiter ? $this->delimiter : $this->getCsvConfiguration()->getDelimiter();
    }

    /**
     * Serializes the elements of the passed array.
     *
     * @param array|null  $unserialized The serialized data
     * @param string|null $delimiter    The delimiter used to serialize the values
     *
     * @return string The serialized array
     */
    public function serialize(array $unserialized = null, $delimiter = null)
    {

        // do nothing, if the passed value is empty or NULL
        if ($unserialized === null || $unserialized === '') {
            return;
        }

        // initialize the delimiter char
        $delimiter = $delimiter ? $delimiter : $this->getDelimiter();

        // load the global configuration
        $csvConfiguration = $this->getCsvConfiguration();

        // initialize the enclosure and escape char
        $enclosure = $csvConfiguration->getEnclosure();
        $escape = $csvConfiguration->getEscape();

        // serialize the passed array into memory and rewind the stream
        $length = fputcsv($fp = fopen('php://memory', 'w'), $unserialized, $delimiter, $enclosure, $escape);
        rewind($fp);

        // load the serialized value - cut off the newnline char
        $serialized = trim(fread($fp, $length), PHP_EOL);
        fclose($fp);

        // return the serialized value
        return $serialized;
    }

    /**
     * Unserializes the elements of the passed string.
     *
     * @param string|null $serialized The value to unserialize
     * @param string|null $delimiter  The delimiter used to unserialize the elements
     *
     * @return array The unserialized values
     */
    public function unserialize($serialized = null, $delimiter = null)
    {

        // do nothing, if the passed value is empty or NULL
        if ($serialized === null || $serialized === '') {
            return;
        }

        // initialize the delimiter char
        $delimiter = $delimiter ? $delimiter : $this->getDelimiter();

        // load the global configuration
        $csvConfiguration = $this->getCsvConfiguration();

        // initialize the enclosure and escape char
        $enclosure = $csvConfiguration->getEnclosure();
        $escape = $csvConfiguration->getEscape();

        // parse and return the found data as array
        return str_getcsv($serialized, $delimiter, $enclosure, $escape);
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string|null $value     The value to extract
     * @param string|null $delimiter The delimiter used to extract the elements
     *
     * @return array|null The exploded values
     * @see \TechDivision\Import\Serializers\ValueCsvSerializer::unserialize()
     */
    public function explode($value = null, $delimiter = null)
    {

        // set the delimiter
        $this->setDelimiter($delimiter);

        // unserialize the value and return it
        return $this->unserialize($value);
    }

    /**
     * Compacts the elements of the passed value by imploding them
     * with the also passed delimiter.
     *
     * @param array|null  $value     The values to compact
     * @param string|null $delimiter The delimiter use to implode the values
     *
     * @return string|null The compatected value
     * @see \TechDivision\Import\Serializers\ValueCsvSerializer::serialize()
     */
    public function implode(array $value = null, $delimiter = null)
    {

        // set the delimiter
        $this->setDelimiter($delimiter);

        // serialize the value and return it
        return $this->serialize($value);
    }
}
