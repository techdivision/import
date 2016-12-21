<?php

/**
 * TechDivision\Import\Configuration
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

namespace TechDivision\Import;

/**
 * The interface for the import configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ConfigurationInterface
{

    /**
     * Return's the operation name that has to be used.
     *
     * @return string The operation name that has to be used
     */
    public function getOperationName();

    /**
     * Return's the database configuration
     *
     * @return TechDivision\Import\Configuration\Database The database configuration
     */
    public function getDatabase();

    /**
     * Return's the ArrayCollection with the configured operations.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operations
     */
    public function getOperations();

    /**
     * Return's the array with the subjects of the operation to use.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the subjects
     * @throws \Exception Is thrown, if no subjects are available for the actual operation
     */
    public function getSubjects();

    /**
     * Return's the Magento installation directory.
     *
     * @return string The Magento installation directory
     */
    public function getInstallationDir();

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @return string The utility class name
     */
    public function getUtilityClassName();

    /**
     * Return's the Magento edition, EE or CE.
     *
     * @return string The Magento edition
     */
    public function getMagentoEdition();

    /**
     * Return's the Magento version, e. g. 2.1.0.
     *
     * @return string The Magento version
     */
    public function getMagentoVersion();
}
