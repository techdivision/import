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

use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface;
use TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface;

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
     * The EAV entity type repository instance.
     *
     * @var \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface
     */
    protected $eavEntityTypeRepository;

    /**
     * Initialize the cache warmer with the repository that has to be warmed.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $repository              The repository to warm
     * @param \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface           $eavEntityTypeRepository The EAV entity type repository instance
     */
    public function __construct(
        EavAttributeOptionValueRepositoryInterface $repository,
        EavEntityTypeRepositoryInterface $eavEntityTypeRepository
    ) {
        $this->repository = $repository;
        $this->eavEntityTypeRepository = $eavEntityTypeRepository;
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

        // load the available EAV entity types
        $eavEntityTypes = $this->eavEntityTypeRepository->findAll();

        // prepare the caches for the statements
        foreach ($eavAttributeOptionValues as $eavAttributeOptionValue) {
            // (re-)sinitialize the array for the cache keys
            $cacheKeys = array();

            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::EAV_ATTRIBUTE_OPTION_VALUE => $eavAttributeOptionValue[$this->repository->getPrimaryKeyName()]);

            // prepare the cache key and add the option value to the cache
            $cacheKeys[$cacheAdapter->cacheKey(
                array(
                    SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID =>
                    array(
                        MemberNames::STORE_ID  => $eavAttributeOptionValue[MemberNames::STORE_ID],
                        MemberNames::OPTION_ID => $eavAttributeOptionValue[MemberNames::OPTION_ID]
                    )
                )
            )] = $uniqueKey;

            // prepare the cache key and add the option value to the cache
            $cacheKeys[$cacheAdapter->cacheKey(
                array(
                    SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE =>
                    array(
                        MemberNames::ATTRIBUTE_CODE => $eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE],
                        MemberNames::STORE_ID       => $eavAttributeOptionValue[MemberNames::STORE_ID],
                        MemberNames::VALUE          => $eavAttributeOptionValue[MemberNames::VALUE]
                    )
                )
            )] = $uniqueKey;

            // prepare the cache key and add the option value to the cache
            foreach ($eavEntityTypes as $eavEntityType) {
                // prepare the cache key and add the option value to the cache
                $cacheKeys[$cacheAdapter->cacheKey(
                    array(
                        SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ENTITY_TYPE_ID_AND_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE =>
                        array(
                            MemberNames::ENTITY_TYPE_ID => $eavEntityType[MemberNames::ENTITY_TYPE_ID],
                            MemberNames::ATTRIBUTE_CODE => $eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE],
                            MemberNames::STORE_ID       => $eavAttributeOptionValue[MemberNames::STORE_ID],
                            MemberNames::VALUE          => $eavAttributeOptionValue[MemberNames::VALUE]
                        )
                    )
                )] = $uniqueKey;
            }

            // add the EAV attribute option value to the cache
            $cacheAdapter->toCache($uniqueKey, $eavAttributeOptionValue, $cacheKeys);
        }
    }
}
