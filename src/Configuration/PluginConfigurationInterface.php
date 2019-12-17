<?php

/**
 * TechDivision\Import\Configuration\PluginConfigurationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * Interface for the plugin configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface PluginConfigurationInterface extends ParamsConfigurationInterface, ImportAdapterAwareConfigurationInterface, ExportAdapterAwareConfigurationInterface
{

    /**
     * Return's the subject's unique DI identifier.
     *
     * @return string The subject's unique DI identifier
     */
    public function getId();

    /**
     * Return's the plugin's name or the ID, if the name is NOT set.
     *
     * @return string The plugin's name
     * @see \TechDivision\Import\Configuration\PluginConfigurationInterface::getId()
     */
    public function getName();

    /**
     * Return's the ArrayCollection with the operation's subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operation's subjects
     */
    public function getSubjects();

    /**
     * Return's the reference to the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration();

    /**
     * Return's the array with the configured listeners.
     *
     * @return array The array with the listeners
     */
    public function getListeners();

    /**
     * Return's the import adapter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface The import adapter configuration instance
     */
    public function getImportAdapter();

    /**
     * Return's the export adapter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface The export adapter configuration instance
     */
    public function getExportAdapter();

    /**
     * Return's the execution context configuration for the actualy plugin configuration.
     *
     * @return \TechDivision\Import\ExecutionContextInterface The execution context to use
     */
    public function getExecutionContext();

    /**
     * Set's the configuration of the operation the plugin has been configured for.
     *
     * @param \\TechDivision\Import\Configuration\OperationConfigurationInterface $operationConfiguration The operation configuration
     *
     * @return void
     */
    public function setOperationConfiguration(OperationConfigurationInterface $operationConfiguration);

    /**
     * Return's the configuration of the operation the plugin has been configured for.
     *
     * @return \TechDivision\Import\Configuration\OperationConfigurationInterface The operation configuration
     */
    public function getOperationConfiguration();

    /**
     * Return's the full opration name, which consists of the Magento edition, the entity type code and the operation name.
     *
     * @param string $separator The separator used to seperate the elements
     *
     * @return string The full operation name
     */
    public function getFullOperationName($separator = '/');
}
