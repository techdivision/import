<?php

/**
 * TechDivision\Import\Repositories\CacheWarmer\EavAttributeOptionValueCacheWarmer
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

namespace TechDivision\Import\Repositories\CacheWarmer;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface;

/**
 * Cache warmer implementation to pre-load EAV attribute option value data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EavAttributeOptionValueCacheWarmer implements CacheWarmerInterface
{

    /**
     * The repository with the cache that has to be warmed.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface
     */
    protected $repository;

    /**
     * Initialize the cache warmer with the repository that has to be warmed.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $repository The repository to warm
     */
    public function __construct(EavAttributeOptionValueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Warms the cache for the passed repository.
     *
     * @return void
     */
    public function warm()
    {

        // load the cache adapter
        /** @var \TechDivision\Import\Cache\CacheAdapterInterface $cacheAdapter */
        $cacheAdapter = $this->repository->getCacheAdapter();

        // load the available EAV attribute option values
        $eavAttributeOptionValues = $this->repository->findAll();

        // prepare the caches for the statements
        foreach ($eavAttributeOptionValues as $eavAttributeOptionValue) {
            // prepare the cache key and add the option value to the cache
            $cacheKey1 = $cacheAdapter->cacheKey(
                SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID,
                array(
                    MemberNames::STORE_ID  => $eavAttributeOptionValue[MemberNames::STORE_ID],
                    MemberNames::OPTION_ID => $eavAttributeOptionValue[MemberNames::OPTION_ID]
                )
            );

            // prepare the cache key and add the option value to the cache
            $cacheKey2 = $cacheAdapter->cacheKey(
                SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE,
                array(
                    MemberNames::ATTRIBUTE_CODE => $eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE],
                    MemberNames::STORE_ID       => $eavAttributeOptionValue[MemberNames::STORE_ID],
                    MemberNames::VALUE          => $eavAttributeOptionValue[MemberNames::VALUE]
                )
            );

            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = $cacheAdapter->cacheKey(
                EavAttributeOptionValueRepositoryInterface::class,
                array($eavAttributeOptionValue[$this->repository->getPrimaryKeyName()])
            );

            // add the EAV attribute option value to the cache
            $cacheAdapter->toCache($uniqueKey, $eavAttributeOptionValue, array($cacheKey1 => $uniqueKey, $cacheKey2 => $uniqueKey));
        }
    }
}
