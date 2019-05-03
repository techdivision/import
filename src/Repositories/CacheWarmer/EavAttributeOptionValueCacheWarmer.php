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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
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

        // load the available EAV attribute option values
        $eavAttributeOptionValues = $this->repository->findAll();

        // prepare the caches for the statements
        foreach ($eavAttributeOptionValues as $eavAttributeOptionValue) {
            // prepare the cache key and add the option value to the cache
            $cacheKey1 = $this->repository->cacheKey(
                SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID,
                array(
                    MemberNames::STORE_ID  => $eavAttributeOptionValue[MemberNames::STORE_ID],
                    MemberNames::OPTION_ID => $eavAttributeOptionValue[MemberNames::OPTION_ID]
                )
            );

            // prepare the cache key and add the option value to the cache
            $cacheKey2 = $this->repository->cacheKey(
                SqlStatementKeys::EAV_ATTRIBUTE_OPTION_VALUE_BY_ATTRIBUTE_CODE_AND_STORE_ID_AND_VALUE,
                array(
                    MemberNames::ATTRIBUTE_CODE => $eavAttributeOptionValue[MemberNames::ATTRIBUTE_CODE],
                    MemberNames::STORE_ID       => $eavAttributeOptionValue[MemberNames::STORE_ID],
                    MemberNames::VALUE          => $eavAttributeOptionValue[MemberNames::VALUE]
                )
            );

            // add the EAV attribute option value to the cache
            $this->repository->toCache(
                $eavAttributeOptionValue[MemberNames::VALUE_ID],
                $eavAttributeOptionValue,
                array(
                    $cacheKey1 => $eavAttributeOptionValue[MemberNames::VALUE_ID],
                    $cacheKey2 => $eavAttributeOptionValue[MemberNames::VALUE_ID]
                )
            );
        }
    }
}
