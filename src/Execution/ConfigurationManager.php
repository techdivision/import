<?php

/**
 * TechDivision\Import\Execution\ConfigurationManager
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

namespace TechDivision\Import\Execution;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\ConfigurationManagerInterface;

/**
 * A simle configuration manager implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationManager implements ConfigurationManagerInterface
{

    /**
     * The configuration instance we want to handle.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    private $configuration;

    /**
     * Mapping for entity type to edition mapping (for configuration purposes only).
     *
     * @var array
     */
    private $entityTypeToEditionMapping = array(
        'eav_attribute'                 => 'general',
        'eav_attribte_set'              => 'general',
        'catalog_product_inventory_msi' => 'general',
        'catalog_product_tier_price'    => 'general',
        'customer_address'              => 'general',
        'customer'                      => 'general'
    );

    /**
     * Initializes the manager with the configuration instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the managed configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's an array with the configurations of the operations that has to be executed.
     *
     * @return \TechDivision\Import\Configuration\OperationConfigurationInterface[] The operation configurations
     */
    public function getOperations()
    {

        // load the configuration instance
        $configuration = $this->getConfiguration();

        // initialize the array for the operations that has to be executed
        $execute = array();

        // load the shortcuts, the operations and the operation names from the configuration
        $shortcuts = $configuration->getShortcuts();
        $operations = $configuration->getOperations();
        $operationNames = $configuration->getOperationNames();

        // query whether or not a shortcut has been passed
        if ($shortcut = $configuration->getShortcut()) {
            // load the Entity Type Code and map it to the Magento Edition
            $magentoEdition = $this->mapEntityTypeToMagentoEdition($entityTypeCode = $configuration->getEntityTypeCode());
            // load the operation names from the shorcuts
            foreach ($shortcuts[$magentoEdition][$entityTypeCode] as $shortcutName => $opNames) {
                // query whether or not the operation has to be executed or nt
                if ($shortcutName === $shortcut) {
                    foreach ($opNames as $operationName) {
                        $operationNames[] = $operationName;
                    }
                }
            }
        }

        // load and initialize the operations by their name
        foreach ($operationNames as $operationName) {
            // explode the shortcut to get Magento Edition, Entity Type Code and Operation Name
            list($edition, $type, $name) = explode('/', $operationName);
            // initialize the execution context with the Magento Edition + Entity Type Code
            $executionContext = new ExecutionContext($edition, $type);
            // load the operations we want to execute
            foreach ($operations[$edition][$type] as $operation) {
                // query whether or not the operation is in the array of operation that has to be executed
                if ($operation->getName() === $name) {
                    // pass the execution context to the operation configuration
                    $operation->setExecutionContext($executionContext);
                    // finally add the operation to the array
                    $execute[] = $operation;
                }
            }
        }

        // return the array with the operations
        return $execute;
    }

    /**
     * Return's the array with the configurations of the plugins that has to be executed.
     *
     * @return \TechDivision\Import\Configuration\PluginConfigurationInterface[] The plugin configurations
     * @throws \Exception Is thrown, if no plugins are available for the actual operation
     */
    public function getPlugins()
    {

        // initialize the array with the plugins that have to be executed
        $plugins = array();

        // load the operations that has to be executed
        $operations = $this->getOperations();

        // load the configuration instance
        $configuration = $this->getConfiguration();

        // initialize the plugin configurations of the selected operations
        foreach ($operations as $operation) {
            // iterate over the operation's plugins and initialize their configuration
            /** @var \TechDivision\Import\Configuration\PluginConfigurationInterface $plugin */
            foreach ($operation->getPlugins() as $plugin) {
                // pass the operation configuration instance to the plugin configuration
                $plugin->setConfiguration($configuration);
                $plugin->setOperationConfiguration($operation);
                // if NO prefix for the move files subject has been set, we use the prefix from the first plugin's subject
                if ($configuration->getMoveFilesPrefix() === null) {
                    // use the prefix of the first subject
                    /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
                    foreach ($plugin->getSubjects() as $subject) {
                        $configuration->setMoveFilesPrefix($subject->getFileResolver()->getPrefix());
                        break;
                    }
                }

                // query whether or not the plugin has subjects configured
                if ($subjects = $plugin->getSubjects()) {
                    // extend the plugin's subjects with the main configuration instance
                    /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
                    foreach ($subjects as $subject) {
                        // set the configuration instance on the subject
                        $subject->setConfiguration($configuration);
                    }
                }

                // finally append the plugin
                $plugins[] = $plugin;
            }
        }

        // query whether or not we've at least ONE plugin to be executed
        if (sizeof($plugins) > 0) {
            return $plugins;
        }

        // throw an exception if no plugins are available
        throw new \Exception(sprintf('Can\'t find any plugins for operation(s) %s', implode(' > ', $configuration->getOperationNames())));
    }

    /**
     * Return's the Entity Type to the configuration specific Magento Edition.
     *
     * @param string $entityType The Entity Type fot map
     *
     * @return string The mapped configuration specific Magento Edition
     */
    protected function mapEntityTypeToMagentoEdition($entityType)
    {

        // load the actual Magento Edition
        $magentoEdition = strtolower($this->getConfiguration()->getMagentoEdition());

        // map the Entity Type to the configuration specific Magento Edition
        if (isset($this->entityTypeToEditionMapping[$entityType])) {
            $magentoEdition = $this->entityTypeToEditionMapping[$entityType];
        }

        // return the Magento Edition
        return $magentoEdition;
    }
}
