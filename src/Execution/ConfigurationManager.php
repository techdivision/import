<?php

/**
 * TechDivision\Import\Execution\ConfigurationManager
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Execution;

use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\ConfigurationManagerInterface;

/**
 * A simle configuration manager implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationManager implements ConfigurationManagerInterface
{

    /**
     * The configuration instance we want to handle.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * Mapping for entity type (for configuration purposes only).
     *
     * @var array
     */
    private $entityTypeMapping = array('none' => 'general');

    /**
     * Mapping for entity type to edition mapping (for configuration purposes only).
     *
     * @var array
     */
    private $entityTypeToEditionMapping = array(
        'eav_attribute'                 => 'general',
        'eav_attribute_set'             => 'general',
        'catalog_product_inventory_msi' => 'general',
        'catalog_product_tier_price'    => 'general',
        'catalog_product_url'           => 'general',
        'customer_address'              => 'general',
        'customer'                      => 'general',
        'general'                       => 'general',
        'none'                          => 'general',
        'debug'                         => 'general'
    );

    /**
     * Initializes the manager with the configuration instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the managed configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
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

        // load the shortcuts from the configuration
        $shortcuts = $configuration->getShortcuts();

        // map the entity type code, if necessary
        $entityTypeCode = $this->mapEntityType($configuration->getEntityTypeCode());

        // query whether or not a shortcut has been passed
        if ($shortcut = $configuration->getShortcut()) {
            // load the entity type code and map it to the Magento edition
            $magentoEdition = $this->mapEntityTypeToMagentoEdition($entityTypeCode);
            // load the operation names from the shorcuts
            foreach ($shortcuts[$magentoEdition][$entityTypeCode] as $shortcutName => $opNames) {
                // query whether or not the operation has to be executed or not
                if ($shortcutName === $shortcut) {
                    foreach ($opNames as $opName) {
                        $configuration->addOperationName($opName);
                    }
                }
            }
        }

        // load the operation names that has to be executed
        $operationNames = $configuration->getOperationNames();

        // load and initialize the operations by their name
        foreach ($operationNames as $operationName) {
            $execute[] = $this->getOperation($operationName);
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

                // query whether or not the plugin has subjects configured
                if ($subjects = $plugin->getSubjects()) {
                    // extend the plugin's subjects with the main configuration instance
                    /** @var \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
                    foreach ($subjects as $subject) {
                        // if the first prefixed subject has not been set, we
                        // use the first one from the first plugin's subject
                        if (empty($configuration->getFirstPrefixedSubject()) && $subject->getFileResolver()->hasPrefix()) {
                            // we use the subject with the first file resolver that has a custom
                            // prefix set to initialize the prefix for the move files subject for
                            $configuration->setFirstPrefixedSubject($subject);
                        }

                        // set the configuration instance on the subject
                        $subject->setConfiguration($configuration);
                    }
                }

                // finally append the plugin
                $plugins[] = $plugin;
            }
        }

        // throw an exception if no plugins are available
        if (empty($plugins)) {
            throw new \Exception(sprintf('Can\'t find any plugins for shortcut "%s"', $configuration->getShortcut()));
        }

        // query whether or not we've at least ONE plugin to be executed
        return $plugins;
    }

    /**
     * Return's the operation with the given name.
     *
     * @param string $operationName The name of the operation to return
     *
     * @return \TechDivision\Import\Configuration\OperationConfigurationInterface The operation instance
     * @throws \Exception Is thrown if the operation with the given name is not available
     */
    protected function getOperation($operationName)
    {

        // explode the shortcut to get Magento Edition, Entity Type Code and Operation Name
        list($edition, $type, $name) = explode('/', $operationName);

        // load the operations from the configuration
        $operations = $this->getConfiguration()->getOperations();

        // query whether or not operations for the Magento edition + entity type code are available
        if (isset($operations[$edition][$type][$name])) {
            // load the operation
            $op = $operations[$edition][$type][$name];
            // pass the name to the operation configuration
            $op->setName($name);
            // initialize and pass the execution context to the operation configuration
            $op->setExecutionContext(new ExecutionContext($edition, $type));
            // finally add the operation to the array
            return $op;
        }

        // throw an exception if the operation is not available
        throw new \Exception(sprintf('Operation "%s" is not available in scope "%s/%s"', $name, $edition, $type));
    }

    /**
     * Return's the entity type mapping.
     *
     * @param string $entityType The entity type to map
     *
     * @return string The mapped entity type
     */
    protected function mapEntityType($entityType)
    {

        // map the entity type, if a mapping is available
        if (isset($this->entityTypeMapping[$entityType])) {
            return $this->entityTypeMapping[$entityType];
        }

        // return the entity type
        return $entityType;
    }

    /**
     * Return's the entity type to the configuration specific Magento edition.
     *
     * @param string $entityType The entity type to map
     *
     * @return string The mapped configuration specific Magento edition
     */
    protected function mapEntityTypeToMagentoEdition($entityType)
    {

        // load the actual Magento edition
        $magentoEdition = strtolower($this->getConfiguration()->getMagentoEdition());

        // map the entity type to the configuration specific Magento edition
        if (isset($this->entityTypeToEditionMapping[$entityType])) {
            $magentoEdition = $this->entityTypeToEditionMapping[$entityType];
        }

        // return the magento edition
        return $magentoEdition;
    }
}
