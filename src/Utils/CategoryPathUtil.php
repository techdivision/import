<?php

/**
 * TechDivision\Import\Utils\CategoryPathUtil
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Serializer\SerializerInterface;

/**
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryPathUtil implements CategoryPathUtilInterface
{

    const SEPARATOR = '/';

    /**
     * The serializer instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     *
     * @param \TechDivision\Import\Serializer\SerializerInterface $serialzier
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function fromProduct(string $path = null) : array
    {


        $categories = array();

        // load and extract the categories from the CSV file
        if ($paths = $this->unserialize($path)) {
            // create a tree of categories that has to be created
            foreach ($paths as $path) {
                // explode the category elements
                $categories[$path] = $this->explode($path);
            }
        }

        return $categories;
    }

    public function fromCategory(string $path = null) : array
    {
        return $this->explode($path);
    }

    public function toProduct(array $paths) : string
    {

        $cats = array();

        foreach ($paths as $elements) {
            $cats[] = $this->implode($elements);
        }

        return $this->serialize($cats);
    }

    /**
     * Create a CSV compatible string from the passed category path.
     *
     * @param string The normalized category path (usually from the DB)
     *
     * @return string The denormalized category path for the import file
     * @see \TechDivision\Import\Utils\CategoryPathUtilInterface::denormalize()
     */
    public function denormalize(string $path) : string
    {
        return $this->serialize(array($path));
    }

    public function normalize(string $path) : string
    {
        return $this->implode($this->explode($path));
    }


    public function explode(string $path) : array
    {
        return $this->unserialize($path, CategoryPathUtil::SEPARATOR);
    }

    public function implode(array $elements) : string
    {
        return $this->serialize($elements, CategoryPathUtil::SEPARATOR);
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
        return $this->serializer->serialize($unserialized, $delimiter);
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
        return $this->serializer->unserialize($serialized, $delimiter);
    }
}
