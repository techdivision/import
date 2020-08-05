<?php

/**
 * TechDivision\Import\Adapter\SerializerTrait
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

namespace TechDivision\Import\Adapter;

use TechDivision\Import\Serializers\SerializerInterface;

/**
 * The trait implementation that provides serializer functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait SerializerTrait
{

    /**
     * The serializer instance to use.
     *
     * @var \TechDivision\Import\Serializers\SerializerInterface
     */
    protected $serializer;

    /**
     * Sets the serializer instance.
     *
     * @param \TechDivision\Import\Serializers\SerializerInterface $serializer The serializer instance
     *
     * @return void
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns the serializer instance.
     *
     * @return \TechDivision\Import\Serializers\SerializerInterface The serializer instance
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string|null $value     The value to extract
     * @param string|null $delimiter The delimiter used to extract the elements
     *
     * @return array|null The exploded values
     */
    public function explode($value = null, $delimiter = null)
    {
        return $this->getSerializer()->explode($value, $delimiter);
    }

    /**
     * Compacts the elements of the passed value by imploding them
     * with the also passed delimiter.
     *
     * @param array|null  $value     The values to compact
     * @param string|null $delimiter The delimiter used to implode the values
     *
     * @return string|null The compatected value
     */
    public function implode(array $value = null, $delimiter = null)
    {
        return $this->getSerializer()->implode($value, $delimiter);
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
        return $this->getSerializer()->serialize($unserialized, $delimiter);
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
        return $this->getSerializer()->unserialize($serialized, $delimiter);
    }
}
