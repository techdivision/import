<?php

/**
 * TechDivision\Import\Services\ConfigurationProcessorInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Services;

/**
 * Interface for a configuration processor implementation that provides the data to access Magento configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ConfigurationProcessorInterface
{

    /**
     * Return's the repository to access store websites.
     *
     * @return \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface The repository instance
     */
    public function getStoreWebsiteRepository();

    /**
     * Return's the repository to access the Magento 2 configuration.
     *
     * @return \TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface The repository instance
     */
    public function getCoreConfigDataRepository();

    /**
     * Return's an array with the available store websites.
     *
     * @return array The array with the available store websites
     */
    public function getStoreWebsites();

    /**
     * Return's an array with the Magento 2 configuration.
     *
     * @return array The Magento 2 configuration
     */
    public function getCoreConfigData();
}
