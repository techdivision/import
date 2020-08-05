<?php

/**
 * TechDivision\Import\Loaders\ConfigurationValueLoader
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

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Configuration\ParamsConfigurationInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Generic loader implementation for configuration values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationValueLoader implements LoaderInterface
{

    /**
     * The configuration key to load the value with.
     *
     * @var string
     */
    protected $configurationKey;

    /**
     * Initializes the loader with the configuration key to load the value with.
     *
     * @param string $configurationKey The configuration key
     */
    public function __construct($configurationKey)
    {
        $this->configurationKey = $configurationKey;
    }

    /**
     * Return's the configuration key to load the value with.
     *
     * @return string The configuration key
     */
    protected function getConfigurationKey()
    {
        return $this->configurationKey;
    }

    /**
     * Loads and returns the configuration value for the key the instance has been initialized with.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the value from
     *
     * @return \ArrayAccess The array with the configuration value
     */
    public function load(ParamsConfigurationInterface $configuration = null)
    {

        // return an empty array if the param has NOT been set
        $values = array();

        // query whether or not an instance has been passed
        if ($configuration === null) {
            return $values;
        }

        // load the values from the plugin configuration recursively
        if ($configuration instanceof PluginConfigurationInterface) {
            $values = array_merge($values, $this->load($configuration->getConfiguration()));
        }

        // load the values from the subject configuration recursively
        if ($configuration instanceof SubjectConfigurationInterface) {
            $values = array_merge($values, $this->load($configuration->getPluginConfiguration()));
        }

        // finally load the values from the actual configuration
        if ($configuration->hasParam($configurationKey = $this->getConfigurationKey())) {
            $values = array_merge($configuration->getParam($configurationKey));
        }

        // return the values
        return $values;
    }
}
