<?php

/**
 * TechDivision\Import\Callbacks\AbstractEavAwareCallback
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

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\EavAwareProcessorInterface;

/**
 * An abstract EAV aware callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractEavAwareCallback extends AbstractCallback
{

    /**
     * The EAV aware processor.
     *
     * @var \TechDivision\Import\Services\EavAwareProcessorInterface
     */
    protected $eavAwareProcessor;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the callback with the passed processor instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface              $configuration     The configuration instance
     * @param \TechDivision\Import\Services\EavAwareProcessorInterface $eavAwareProcessor The processor instance
     */
    public function __construct(ConfigurationInterface $configuration, EavAwareProcessorInterface $eavAwareProcessor)
    {
        $this->configuration = $configuration;
        $this->eavAwareProcessor = $eavAwareProcessor;
    }

    /**
     * Return's the EAV aware processor instance.
     *
     * @return \TechDivision\Import\Services\EavAwareProcessorInterface The processor instance
     */
    protected function getEavAwareProcessor()
    {
        return $this->eavAwareProcessor;
    }

    /**
     * Returns the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the configured entity type code.
     *
     * @return string The entity type code from the configuration
     */
    protected function getEntityTypeCode()
    {
        return $this->getConfiguration()->getEntityTypeCode();
    }

    /**
     * Returns the entity type ID for the configured entity type code.
     *
     * @return integer The entity type ID of the configured entity type
     */
    protected function getEntityTypeId()
    {

        // load the entity type for the configured entity type code
        $entityType = $this->getEavAwareProcessor()->loadEavEntityTypeByEntityTypeCode($this->getEntityTypeCode());

        // return the entity type ID
        return $entityType[MemberNames::ENTITY_TYPE_ID];
    }

    /**
     * Load's and return's the EAV attribute option value with the passed entity type ID, code, store ID and value.
     *
     * @param string  $entityTypeId  The entity type ID of the EAV attribute to load the option value for
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    protected function loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value)
    {
        return $this->getEavAwareProcessor()->loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value);
    }
}
