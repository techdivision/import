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

        // prepare the caches for the statements
        foreach ($this->repository->findAll() as $optionValue) {
            // the parameters of the EAV attribute option to load
            $params = array(
                MemberNames::STORE_ID       => $optionValue[MemberNames::STORE_ID],
                MemberNames::OPTION_ID      => $optionValue[MemberNames::OPTION_ID]
            );

            // load the repositories utility class name
            $utilityClassName = $this->repository->getUtilityClassName();

            // prepare the cache key and add the option value to the cache
            $cacheKey = $this->repository->cacheKey($utilityClassName::EAV_ATTRIBUTE_OPTION_VALUE_BY_OPTION_ID_AND_STORE_ID, $params);
            $this->repository->toCache($cacheKey, $optionValue);
        }
    }
}
