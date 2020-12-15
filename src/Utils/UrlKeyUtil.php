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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Utility class that provides functionality to make URL keys unique.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
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
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Services\UrlKeyAwareProcessorInterface $urlKeyAwareProcessor The URL key aware processor instance
     */
    public function __construct(UrlKeyAwareProcessorInterface $urlKeyAwareProcessor)
    {
        $this->urlKeyAwareProcessor = $urlKeyAwareProcessor;
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
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject The subject to make the URL key unique for
     * @param string                                                    $urlKey  The URL key to make unique
     * @param string|null                                               $urlPath The URL path to make unique (only used for categories)
     *
     * @return string The unique URL key
     */
    public function makeUnique(UrlKeyAwareSubjectInterface $subject, string $urlKey, string $urlPath = null) : string
    {

        // initialize the store view ID, use the default store view if no store view has
        // been set, because the default url_key value has been set in default store view
        $storeId = $subject->getRowStoreId();

        // initialize the counter
        $counter = 0;

        // initialize the counters
        $matchingCounters = array();
        $notMatchingCounters = array();

        // pre-initialze the URL by concatenating path and/or key to query for
        $url = $urlPath ? sprintf('%s/%s', $urlPath, $urlKey) : $urlKey;

        do {
            // try to load the attribute
            $urlRewrite = $this->loadUrlRewriteByRequestPathAndStoreId($url, $storeId);

            // try to load the entity's URL key
            if ($urlRewrite) {
                // this IS the URL key of the passed entity
                if ($subject->isUrlKeyOf($urlRewrite)) {
                    $matchingCounters[] = $counter;
                } else {
                    $notMatchingCounters[] = $counter;
                }

                // prepare the next URL key to query for
                $url = sprintf('%s-%d', $urlKey, ++$counter);
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
            // create a new URL key by raising the counter
            $newCounter = end($notMatchingCounters);
            $urlKey = sprintf('%s-%d', $urlKey, ++$newCounter);
        }

        // return the passed URL key, if NOT
        return $urlKey;
    }
}
