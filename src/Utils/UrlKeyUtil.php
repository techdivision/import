<?php

/**
 * TechDivision\Import\Utils\UrlKeyUtil
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

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Utility class that provides functionality to make URL keys unique.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlKeyUtil implements UrlKeyUtilInterface
{

    /**
     * The URL key aware processor instance.
     *
     * \TechDivision\Import\Services\UrlKeyAwareProcessorInterface
     */
    protected $urlKeyAwareProcessor;

    /**
     * The array with the entity type and store view specific suffixes.
     *
     * @var array
     */
    protected $suffixes = array();

    /**
     * The URL rewrite entity type to use.
     *
     * @var \TechDivision\Import\Utils\EnumInterface
     */
    protected $urlRewriteEntityType;

    /**
     * The array with the entity type code > configuration key mapping.
     *
     * @var array
     */
    protected $entityTypeCodeToConfigKeyMapping = array(
        EntityTypeCodes::CATALOG_PRODUCT  => CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX,
        EntityTypeCodes::CATALOG_CATEGORY => CoreConfigDataKeys::CATALOG_SEO_CATEGORY_URL_SUFFIX
    );

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Services\UrlKeyAwareProcessorInterface $urlKeyAwareProcessor The URL key aware processor instance
     * @param \TechDivision\Import\Loaders\LoaderInterface                $coreConfigDataLoader The core config data loader instance
     * @param \TechDivision\Import\Loaders\LoaderInterface                $storeIdLoader        The core config data loader instance
     * @param \TechDivision\Import\Utils\EnumInterface                    $urlRewriteEntityType The URL rewrite entity type to use
     */
    public function __construct(
        UrlKeyAwareProcessorInterface $urlKeyAwareProcessor,
        LoaderInterface $coreConfigDataLoader,
        LoaderInterface $storeIdLoader,
        EnumInterface $urlRewriteEntityType
    ) {

        // initialize the URL kew aware processor instance
        $this->urlKeyAwareProcessor = $urlKeyAwareProcessor;
        $this->urlRewriteEntityType = $urlRewriteEntityType;

        // load the available stores
        $storeIds = $storeIdLoader->load();

        // initialize the URL suffixs from the Magento core configuration
        foreach ($storeIds as $storeId) {
            // prepare the array with the entity type and store ID specific suffixes
            foreach ($this->entityTypeCodeToConfigKeyMapping as $entityTypeCode => $configKey) {
                // load the suffix for the given entity type => configuration key and store ID
                $suffix = $coreConfigDataLoader->load($configKey, '.html', ScopeKeys::SCOPE_DEFAULT, $storeId);
                // register the suffux in the array
                $this->suffixes[$entityTypeCode][$storeId] = $suffix;
            }
        }
    }

    /**
     * Returns the URL key aware processor instance.
     *
     * @return \TechDivision\Import\Services\UrlKeyAwareProcessorInterface The processor instance
     */
    protected function getUrlKeyAwareProcessor()
    {
        return $this->urlKeyAwareProcessor;
    }

    /**
     * Load's and return's the URL rewrite for the given request path and store ID
     *
     * @param string $requestPath The request path to load the URL rewrite for
     * @param int    $storeId     The store ID to load the URL rewrite for
     *
     * @return string|null The URL rewrite found for the given request path and store ID
     */
    protected function loadUrlRewriteByRequestPathAndStoreId(string $requestPath, int $storeId)
    {
        return $this->getUrlKeyAwareProcessor()->loadUrlRewriteByRequestPathAndStoreId($requestPath, $storeId);
    }

    /**
     * Make's the passed URL key unique by adding/raising a number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject The subject to make the URL key unique for
     * @param array                                                     $entity  The entity to make the URL key unique for
     * @param string                                                    $urlKey  The URL key to make unique
     * @param string|null                                               $urlPath The URL path to make unique (only used for categories)
     *
     * @return string The unique URL key
     */
    protected function doMakeUnique(UrlKeyAwareSubjectInterface $subject, array $entity, string $urlKey, string $urlPath = null) : string
    {

        // initialize the store view ID, use the default store view if no store view has
        // been set, because the default url_key value has been set in default store view
        $storeId = (int) $subject->getRowStoreId();
        $entityTypeCode = $subject->getEntityTypeCode();

        // initialize entity ID + type from the passed entity
        $entityId = (int) $entity[MemberNames::ENTITY_ID];
        $entityType = (string) $this->urlRewriteEntityType;

        // initialize the counter
        $counter = 0;

        // initialize the counters
        $matchingCounters = array();
        $notMatchingCounters = array();

        // pre-initialze the URL by concatenating path and/or key to query for
        $url = $urlPath ? sprintf('%s/%s', $urlPath, $urlKey) : $urlKey;

        do {
            // prepare the request path to load an existing URL rewrite
            $requestPath = sprintf('%s%s', $url, $this->suffixes[$entityTypeCode][$storeId]);
            // try to load an existing URL rewrite
            $urlRewrite = $this->loadUrlRewriteByRequestPathAndStoreId($requestPath, $storeId);

            // query whether or not an entity with the given
            // request path and store ID is available
            if ($urlRewrite) {
                // if yes, query if this IS the URL key of the passed entity
                if (((int) $urlRewrite[MemberNames::ENTITY_ID]   === $entityId) &&
                    ((int) $urlRewrite[MemberNames::STORE_ID]    === $storeId) &&
                           $urlRewrite[MemberNames::ENTITY_TYPE] === $entityType
                ) {
                    // add the matching counter
                    $matchingCounters[] = $counter;
                    // stop further processing here, because we've a matching
                    // URL key and that's all we want for the moment
                    break;
                } else {
                    $notMatchingCounters[] = $counter;
                }

                // prepare the next URL key to query for
                $url = sprintf('%s-%d', $urlKey, ++$counter);
            } else {
                // we've temporary persist a dummy URL rewrite to keep track of the new URL key, e. g. for
                // the case the import contains another product or category that wants to use the same one
                $this->getUrlKeyAwareProcessor()->persistUrlRewrite(
                    array(
                        MemberNames::URL_REWRITE_ID => md5(sprintf('%d-%s', $storeId, $requestPath)),
                        MemberNames::REDIRECT_TYPE  => 0,
                        MemberNames::STORE_ID       => $storeId,
                        MemberNames::ENTITY_ID      => $entityId,
                        MemberNames::REQUEST_PATH   => $requestPath,
                        MemberNames::ENTITY_TYPE    => $entityType,
                        EntityStatus::MEMBER_NAME   => EntityStatus::STATUS_CREATE
                    )
                );
            }
        } while ($urlRewrite);

        // sort the array ascending according to the counter
        asort($matchingCounters);
        asort($notMatchingCounters);

        // this IS the URL key of the passed entity => we've an UPDATE
        if (sizeof($matchingCounters) > 0) {
            // load highest counter
            $counter = end($matchingCounters);
            // if the counter is > 0, we've to append it to the new URL key
            if ($counter > 0) {
                $urlKey = sprintf('%s-%d', $urlKey, $counter);
            }
        } elseif (sizeof($notMatchingCounters) > 0) {
            // load the last entry that contains the
            // the last NOT matching counter
            $newCounter = end($notMatchingCounters);
            // create a new URL key by raising the counter
            $urlKey = sprintf('%s-%d', $urlKey, ++$newCounter);
        }

        // return the passed URL key, if NOT
        return $urlKey;
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject  The subject to make the URL key unique for
     * @param array                                                     $entity   The entity to make the URL key unique for
     * @param string                                                    $urlKey   The URL key to make unique
     * @param array                                                     $urlPaths The URL paths to make unique
     *
     * @return string The unique URL key
     */
    public function makeUnique(UrlKeyAwareSubjectInterface $subject, array $entity, string $urlKey, array $urlPaths = array()) : string
    {

        // in general, we want to start at -1, because if NO URL paths has been given
        // e. g. we've a product or a root category, we want to make sure that we've
        // no URL collisions.
        $i = -1;

        // only in case we've a category AND URL paths have been given, we start at 0,
        // because the we always want to make sure that also the URL path will be taken
        // into account when we make the URL key unique.
        if ($this->urlRewriteEntityType->equals(UrlRewriteEntityType::CATEGORY) && sizeof($urlPaths) > 0) {
            $i = 0;
        }

        // iterate over the passed URL paths
        // and try to find a unique URL key
        for ($i; $i < sizeof($urlPaths); $i++) {
            // try to make the URL key unique for the given URL path
            $proposedUrlKey = $this->doMakeUnique($subject, $entity, $urlKey, isset($urlPaths[$i]) ? $urlPaths[$i] : null);

            // if the URL key is NOT the same as the passed one or with the parent URL path
            // it can NOT be used, so we've to persist it temporarily and try it again for
            // all the other URL paths until we found one that works with every URL path
            if ($urlKey !== $proposedUrlKey) {
                // temporarily persist the URL key
                $urlKey = $proposedUrlKey;
                // reset the counter and restart the
                // iteration with the first URL path
                $i = -2;
            }
        }

        // return the unique URL key
        return $urlKey;
    }

    /**
     * Load the url_key if exists
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject      The subject to make the URL key unique for
     * @param int                                                       $primaryKeyId The ID from category or product
     *
     * @return string|null The URL key
     */
    public function loadUrlKey(UrlKeyAwareSubjectInterface $subject, $primaryKeyId)
    {

        // initialize the entity type ID
        $entityType = $subject->getEntityType();
        $entityTypeId = (integer) $entityType[MemberNames::ENTITY_TYPE_ID];

        // initialize the store view ID, use the admin store view if no store view has
        // been set, because the default url_key value has been set in admin store view
        $storeId = $subject->getRowStoreId(StoreViewCodes::ADMIN);

        // try to load the attribute
        $attribute = $this->getUrlKeyAwareProcessor()
            ->loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKey(
                MemberNames::URL_KEY,
                $entityTypeId,
                $storeId,
                $primaryKeyId
            );

        // return the attribute value or null, if not available
        return $attribute ? $attribute['value'] : null;
    }
}
