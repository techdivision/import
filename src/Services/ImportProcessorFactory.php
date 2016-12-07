<?php

/**
 * TechDivision\Import\Services\ImportProcessorFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Services;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Repositories\StoreRepository;
use TechDivision\Import\Repositories\TaxClassRepository;
use TechDivision\Import\Repositories\LinkTypeRepository;
use TechDivision\Import\Repositories\CategoryRepository;
use TechDivision\Import\Repositories\StoreWebsiteRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Repositories\EavAttributeSetRepository;

/**
 * Factory to create a new import processor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ImportProcessorFactory
{

    /**
     * Factory method to create a new import processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return object The processor instance
     */
    public static function factory(\PDO $connection, ConfigurationInterface $configuration)
    {

        // extract Magento edition/version
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides category query functionality
        $categoryRepository = new CategoryRepository();
        $categoryRepository->setUtilityClassName($utilityClassName);
        $categoryRepository->setConnection($connection);
        $categoryRepository->init();

        // initialize the repository that provides category varchar value query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository();
        $categoryVarcharRepository->setUtilityClassName($utilityClassName);
        $categoryVarcharRepository->setConnection($connection);
        $categoryVarcharRepository->init();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setUtilityClassName($utilityClassName);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides EAV attribute set query functionality
        $eavAttributeSetRepository = new EavAttributeSetRepository();
        $eavAttributeSetRepository->setUtilityClassName($utilityClassName);
        $eavAttributeSetRepository->setConnection($connection);
        $eavAttributeSetRepository->init();

        // initialize the repository that provides store query functionality
        $storeRepository = new StoreRepository();
        $storeRepository->setUtilityClassName($utilityClassName);
        $storeRepository->setConnection($connection);
        $storeRepository->init();

        // initialize the repository that provides store website query functionality
        $storeWebsiteRepository = new StoreWebsiteRepository();
        $storeWebsiteRepository->setUtilityClassName($utilityClassName);
        $storeWebsiteRepository->setConnection($connection);
        $storeWebsiteRepository->init();

        // initialize the repository that provides tax class query functionality
        $taxClassRepository = new TaxClassRepository();
        $taxClassRepository->setUtilityClassName($utilityClassName);
        $taxClassRepository->setConnection($connection);
        $taxClassRepository->init();

        // initialize the repository that provides link type query functionality
        $linkTypeRepository = new LinkTypeRepository();
        $linkTypeRepository->setUtilityClassName($utilityClassName);
        $linkTypeRepository->setConnection($connection);
        $linkTypeRepository->init();

        // initialize the import processor
        $importProcessor = new ImportProcessor();
        $importProcessor->setConnection($connection);
        $importProcessor->setCategoryRepository($categoryRepository);
        $importProcessor->setCategoryVarcharRepository($categoryVarcharRepository);
        $importProcessor->setEavAttributeRepository($eavAttributeRepository);
        $importProcessor->setEavAttributeSetRepository($eavAttributeSetRepository);
        $importProcessor->setStoreRepository($storeRepository);
        $importProcessor->setStoreWebsiteRepository($storeWebsiteRepository);
        $importProcessor->setTaxClassRepository($taxClassRepository);
        $importProcessor->setLinkTypeRepository($linkTypeRepository);

        // return the initialize import processor instance
        return $importProcessor;
    }
}
