<?php

/**
 * TechDivision\Import\Utils\CacheKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Cache\Utils\CacheKeysInterface;

/**
 * A utility class that contains the cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The cache key for URL rewrites.
     *
     * @var string
     */
    const URL_REWRITE = 'url_rewrite';

    /**
     * The cache key for the sequences.
     *
     * @var string
     */
    const SEQUENCES = 'sequences';

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
                CacheKeys::URL_REWRITE,
                CacheKeys::SEQUENCES,
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
        // @phpstan-ignore-next-line
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
