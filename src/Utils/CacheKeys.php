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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * A utility class that contains the cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheKeys extends \ArrayObject implements CacheKeysInterface
{

    /**
     * The cache key for import status.
     *
     * @var string
     */
    const STATUS = 'status';

    /**
     * The cache key for references.
     *
     * @var string
     */
    const REFERENCES = 'references';

    /**
     * The cache key for artefacts.
     *
     * @var string
     */
    const ARTEFACTS = 'artefacts';

    /**
     * The cache key for EAV attribute option values.
     *
     * @var string
     */
    const EAV_ATTRIBUTE_OPTION_VALUE = 'eav_attribute_option_value';

    /**
     * The instance cache key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Initializes the instance with the passed cache key.
     *
     * @param string $cacheKey  The cache key use
     * @param array  $cacheKeys Additional cache keys
     */
    public function __construct($cacheKey, array $cacheKeys = array())
    {

        // merge the passed cache keys with the one from this class
        $mergedCacheKeys = array_merge(
            array(
                CacheKeys::STATUS,
                CacheKeys::REFERENCES,
                CacheKeys::ARTEFACTS,
                CacheKeys::EAV_ATTRIBUTE_OPTION_VALUE
            ),
            $cacheKeys
        );

        // pass them to the parent instance
        parent::__construct($mergedCacheKeys);

        // query whether or not we've a valid cache key
        if ($this->isCacheKey($cacheKey)) {
            $this->cacheKey = $cacheKey;
        } else {
            throw new \InvalidArgumentException(sprintf('Found invalid cache key "%s"', $cacheKey));
        }
    }

    /**
     * Factory method to create a new cache key instance.
     *
     * @param string $cacheKey The cache key to use
     *
     * @return \TechDivision\Import\Utils\CacheKeys The cache key instance
     */
    public static function get($cacheKey)
    {
        return new static($cacheKey);
    }

    /**
     * Query whether or not the passed cache key is valid.
     *
     * @param string $cacheKey The cache key to query for
     *
     * @return boolean TRUE if the cache key is valid, else FALSE
     */
    public function isCacheKey($cacheKey)
    {
        return in_array($cacheKey, (array) $this);
    }

    /**
     * Returns the cache key of the actual instance.
     *
     * @return string The cache key
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
