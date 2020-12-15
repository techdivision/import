<?php

/**
 * TechDivision\Import\Services\ImportProcessor
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

namespace TechDivision\Import\Services;

use TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface;
use TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface;

/**
 * Processor implementation to load Magento configuration data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConfigurationProcessor implements ConfigurationProcessorInterface
{

    /**
     * The repository to access store websites.
     *
     * @var \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface
     */
    protected $storeWebsiteRepository;

    /**
     * The core config data loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $coreConfigDataLoader;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface   $storeWebsiteRepository   The repository to access store websites
     * @param \TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface $coreConfigDataRepository The repository to access the configuration
     */
    public function __construct(
        StoreWebsiteRepositoryInterface $storeWebsiteRepository,
        CoreConfigDataRepositoryInterface $coreConfigDataRepository
    ) {
        $this->setStoreWebsiteRepository($storeWebsiteRepository);
        $this->setCoreConfigDataRepository($coreConfigDataRepository);
    }

    /**
     * Set's the repository to access store websites.
     *
     * @param \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface $storeWebsiteRepository The repository the access store websites
     *
     * @return void
     */
    public function setStoreWebsiteRepository(StoreWebsiteRepositoryInterface $storeWebsiteRepository)
    {
        $this->storeWebsiteRepository = $storeWebsiteRepository;
    }

    /**
     * Return's the repository to access store websites.
     *
     * @return \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface The repository instance
     */
    public function getStoreWebsiteRepository()
    {
        return $this->storeWebsiteRepository;
    }

    /**
     * Set's the repository to access the Magento 2 configuration.
     *
     * @param \TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface $coreConfigDataRepository The repository to access the Magento 2 configuration
     *
     * @return void
     */
    public function setCoreConfigDataRepository(CoreConfigDataRepositoryInterface $coreConfigDataRepository)
    {
        $this->coreConfigDataRepository = $coreConfigDataRepository;
    }

    /**
     * Return's the repository to access the Magento 2 configuration.
     *
     * @return \TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface The repository instance
     */
    public function getCoreConfigDataRepository()
    {
        return $this->coreConfigDataRepository;
    }

    /**
     * Return's an array with the available store websites.
     *
     * @return array The array with the available store websites
     */
    public function getStoreWebsites()
    {
        return $this->getStoreWebsiteRepository()->findAll();
    }

    /**
     * Return's an array with the Magento 2 configuration.
     *
     * @return array The Magento 2 configuration
     */
    public function getCoreConfigData()
    {
        return $this->getCoreConfigDataRepository()->findAll();
    }
}
