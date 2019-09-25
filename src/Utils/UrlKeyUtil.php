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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Utility class that provides functionality to make URL keys unique.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlKeyUtil implements UrlKeyUtilInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The URL key aware processor instance.
     *
     * \TechDivision\Import\Services\UrlKeyAwareProcessorInterface
     */
    protected $urlKeyAwareProcessor;

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface                 $configuration        The configuration instance
     * @param \TechDivision\Import\Services\UrlKeyAwareProcessorInterface $urlKeyAwareProcessor The URL key aware processor instance
     */
    public function __construct(
        ConfigurationInterface $configuration,
        UrlKeyAwareProcessorInterface $urlKeyAwareProcessor
    ) {
        $this->configuration = $configuration;
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
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $value         The value of the varchar attribute
     *
     * @return array|null The varchar attribute
     */
    protected function loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {
        return $this->getUrlKeyAwareProcessor()
                    ->loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue(
                        $attributeCode,
                        $entityTypeId,
                        $storeId,
                        $value
                    );
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param string $urlKey The URL key to make unique
     *
     * @return string The unique URL key
     */
    public function makeUnique(UrlKeyAwareSubjectInterface $subject, $urlKey)
    {

        // initialize the entity type ID
        $entityType = $subject->getEntityType();
        $entityTypeId = (integer) $entityType[MemberNames::ENTITY_TYPE_ID];

        // initialize the store view ID, use the admin store view if no store view has
        // been set, because the default url_key value has been set in admin store view
        $storeId = $subject->getRowStoreId(StoreViewCodes::ADMIN);

        // initialize the counter
        $counter = 0;

        // initialize the counters
        $matchingCounters = array();
        $notMatchingCounters = array();

        // pre-initialze the URL key to query for
        $value = $urlKey;

        do {
            // try to load the attribute
            $attribute =
                $this->loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue(
                    MemberNames::URL_KEY,
                    $entityTypeId,
                    $storeId,
                    $value
                );

            // try to load the entity's URL key
            if ($attribute) {
                // this IS the URL key of the passed entity
                if ($subject->isUrlKeyOf($attribute)) {
                    $matchingCounters[] = $counter;
                } else {
                    $notMatchingCounters[] = $counter;
                }

                // prepare the next URL key to query for
                $value = sprintf('%s-%d', $urlKey, ++$counter);
            }
        } while ($attribute);

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
