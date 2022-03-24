<?php

/**
 * TechDivision\Import\Loaders\CoreConfigDataLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Services\ConfigurationProcessorInterface;

/**
 * Generic loader implementation for the Magento core configuration data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CoreConfigDataLoader implements LoaderInterface
{

    /**
     * The available websites.
     *
     * @var array
     */
    private $storeWebsites = array();

    /**
     * The core config data from the Magento instance.
     *
     * @var array
     */
    private $coreConfigData = array();

    /**
     * The UID generator for the core config data.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    private $coreConfigDataUidGenerator;

    /**
     * Initialize the loader with the configuration processor and the UID generator for the core config data.
     *
     * @param \TechDivision\Import\Services\ConfigurationProcessorInterface $configurationProcessor     The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface      $coreConfigDataUidGenerator The UID generator for the core config data
     */
    public function __construct(
        ConfigurationProcessorInterface $configurationProcessor,
        GeneratorInterface $coreConfigDataUidGenerator
    ) {

        // initialize the UUID generator
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;

        // load the core configuration data from the Magento instance
        $this->coreConfigData = $configurationProcessor->getCoreConfigData();

        // load the store website data from the Magento instance
        $this->storeWebsites = $configurationProcessor->getStoreWebsites();
    }

    /**
     * Return's the Magento configuration value.
     *
     * @param string  $path    The Magento path of the requested configuration value
     * @param mixed   $default The default value that has to be returned, if the requested configuration value is not set
     * @param string  $scope   The scope the configuration value has been set
     * @param integer $scopeId The scope ID the configuration value has been set
     *
     * @return mixed The configuration value
     * @throws \Exception Is thrown, if nor a value can be found or a default value has been passed
     */
    public function load($path = null, $default = null, $scope = ScopeKeys::SCOPE_DEFAULT, $scopeId = 0)
    {

        // initialize the core config data
        $coreConfigData = array(
            MemberNames::PATH => $path,
            MemberNames::SCOPE => $scope,
            MemberNames::SCOPE_ID => $scopeId
        );

        // generate the UID from the passed data
        $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

        // iterate over the core config data and try to find the requested configuration value
        if (isset($this->coreConfigData[$uniqueIdentifier]) && array_key_exists(MemberNames::VALUE, $this->coreConfigData[$uniqueIdentifier])) {
            return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE] ?? '';
        }

        // query whether or not we've to query for the configuration value on fallback level 'websites' also
        if ($scope === ScopeKeys::SCOPE_STORES) {
            // query whether or not the website with the passed ID is available
            foreach ($this->storeWebsites as $storeWebsite) {
                if ($storeWebsite[MemberNames::WEBSITE_ID] === $scopeId) {
                    // replace scope with 'websites' and website ID
                    $coreConfigData = array_merge(
                        $coreConfigData,
                        array(
                            MemberNames::SCOPE    => ScopeKeys::SCOPE_WEBSITES,
                            MemberNames::SCOPE_ID => $storeWebsite[MemberNames::WEBSITE_ID]
                        )
                    );

                    // generate the UID from the passed data, merged with the 'websites' scope and ID
                    $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

                    // query whether or not, the configuration value on 'websites' level
                    if (isset($this->coreConfigData[$uniqueIdentifier]) && array_key_exists(MemberNames::VALUE, $this->coreConfigData[$uniqueIdentifier])) {
                        return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE] ?? '';
                    }
                }
            }
        }

        // replace scope with 'default' and scope ID '0'
        $coreConfigData = array_merge(
            $coreConfigData,
            array(
                MemberNames::SCOPE    => ScopeKeys::SCOPE_DEFAULT,
                MemberNames::SCOPE_ID => 0
            )
        );

        // generate the UID from the passed data, merged with the 'default' scope and ID 0
        $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);

        // query whether or not, the configuration value on 'default' level
        if (isset($this->coreConfigData[$uniqueIdentifier]) && array_key_exists(MemberNames::VALUE, $this->coreConfigData[$uniqueIdentifier])) {
            return $this->coreConfigData[$uniqueIdentifier][MemberNames::VALUE] ?? '';
        }

        // if not, return the passed default value
        if ($default !== null) {
            return $default;
        }

        // throw an exception if no value can be found
        // in the Magento configuration
        throw new \Exception(
            sprintf(
                'Can\'t find a value for configuration "%s-%s-%d" in "core_config_data"',
                $path,
                $scope,
                $scopeId
            )
        );
    }
}
