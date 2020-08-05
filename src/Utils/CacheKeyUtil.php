<?php

/**
 * TechDivision\Import\Utils\CacheKeys
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * A utility class to create cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheKeyUtil implements CacheKeyUtilInterface
{

    /**
     * The separator for cache key elements.
     *
     * @var string
     */
    const SEPARATOR = '-';

    /**
     * The prefix to use.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Initializes the cache key util with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->prefix = $configuration->getSerial();
    }

    /**
     * Creates a unique cache key from the passed data.
     *
     * @param mixed   $data      The date to create the cache key from
     * @param boolean $usePrefix Flag to signal using the prefix or not
     *
     * @return string The generated cache key
     * @throws \Exception Is thrown if the passed data is not supported to create a cache key from
     */
    public function cacheKey($data, $usePrefix = true)
    {

        // return the cache key for scalar data
        if (is_scalar($data)) {
            return $this->prefix($this->scalarKey($data), $usePrefix);
        }

        // return the cache key for an array
        if (is_array($data)) {
            return $this->prefix($this->arrayKey($data), $usePrefix);
        }

        // return the cache key for an object
        if (is_object($data)) {
            return $this->prefix($this->objectKey($data), $usePrefix);
        }

        // throw an exception if the passed data type is NOT supported
        throw new \Exception(
            sprintf('Found not supported data type "%s" when preparing cache key', gettype($data))
        );
    }

    /**
     * Prefixes the cache key, e. g. with the products serial.
     *
     * @param string  $cacheKey  The cache key to prefix
     * @param boolean $usePrefix Flag to signal using the prefix or not
     *
     * @return string The prefixed cache key
     */
    protected function prefix($cacheKey, $usePrefix)
    {
        return $usePrefix ? $this->prefix . $cacheKey : ltrim($cacheKey, CacheKeyUtil::SEPARATOR);
    }

    /**
     * Creates a cache key for a scalar values.
     *
     * @param mixed $data The scalar value to c
     *
     * @return string
     */
    protected function scalarKey($data)
    {
        return CacheKeyUtil::SEPARATOR . $data;
    }

    /**
     * Creates a cache key from the passed object.
     *
     * The object MUST implement the __toString method, else an exception will be thrown.
     *
     * @param object $data The object to create the cache key for
     *
     * @return string The cache key from the object's __toString method
     * @throws \Exception Is thrown, if the object doesn't implement the __toString method
     */
    protected function objectKey($data)
    {

        // query whether or not the object implements the __toString method
        if (method_exists($data, '__toString')) {
            return CacheKeyUtil::SEPARATOR . $data->__toString();
        }

        // throw an exception if not
        throw new \Exception(
            sprintf('Class "%s" doesn\'t implement necessary method "__toString" to create a cache key', get_class($data))
        );
    }

    /**
     * Creates a cache key from the passed array.
     *
     * @param array $data The array to create the cache key for
     *
     * @return string The cache key created from the array's key => value pairs
     * @throws \Exception Is thrown, if the array contains unsupported values or the array is empty
     */
    protected function arrayKey(array $data)
    {

        // query whether or not the array contains data
        if (sizeof($data) > 0) {
            // intialize the cache key
            $cacheKey = '';
            // iterate over the array's values, prepare and finally return the cache key
            foreach ($data as $key => $value) {
                if (is_scalar($value)) {
                    $cacheKey .= $this->scalarKey($key) . $this->scalarKey($value);
                } elseif (is_array($value)) {
                    $cacheKey .= $this->scalarKey($key) . $this->arrayKey($value);
                } elseif (is_object($data)) {
                    $cacheKey .= $this->scalarKey($key) . $this->objectKey($value);
                } else {
                    throw new \Exception(
                        sprintf('Found not supported data type "%s" for key "%s" in array when preparing cache key', $key, gettype($value))
                    );
                }
            }

            // return the cache key
            return $cacheKey;
        }

        // throw an exception if the array contains no data
        throw new \Exception('Array to create the cache key from contains no data');
    }
}
