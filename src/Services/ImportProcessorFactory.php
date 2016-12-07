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
class ImportProcessorFactory extends AbstractProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Services\ImportProcessor';
    }

    /**
     * Factory method to create a new import processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return object The processor instance
     */
    public function factory(\PDO $connection, ConfigurationInterface $configuration)
    {

        // extract Magento edition/version
        $magentoEdition = $configuration->getMagentoEdition();
        $magentoVersion = $configuration->getMagentoVersion();

        // initialize the repository that provides category query functionality
        $categoryRepository = new CategoryRepository();
        $categoryRepository->setMagentoEdition($magentoEdition);
        $categoryRepository->setMagentoVersion($magentoVersion);
        $categoryRepository->setConnection($connection);
        $categoryRepository->init();

        // initialize the repository that provides category varchar value query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository();
        $categoryVarcharRepository->setMagentoEdition($magentoEdition);
        $categoryVarcharRepository->setMagentoVersion($magentoVersion);
        $categoryVarcharRepository->setConnection($connection);
        $categoryVarcharRepository->init();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setMagentoEdition($magentoEdition);
        $eavAttributeRepository->setMagentoVersion($magentoVersion);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides EAV attribute set query functionality
        $eavAttributeSetRepository = new EavAttributeSetRepository();
        $eavAttributeSetRepository->setMagentoEdition($magentoEdition);
        $eavAttributeSetRepository->setMagentoVersion($magentoVersion);
        $eavAttributeSetRepository->setConnection($connection);
        $eavAttributeSetRepository->init();

        // initialize the repository that provides store query functionality
        $storeRepository = new StoreRepository();
        $storeRepository->setMagentoEdition($magentoEdition);
        $storeRepository->setMagentoVersion($magentoVersion);
        $storeRepository->setConnection($connection);
        $storeRepository->init();

        // initialize the repository that provides store website query functionality
        $storeWebsiteRepository = new StoreWebsiteRepository();
        $storeWebsiteRepository->setMagentoEdition($magentoEdition);
        $storeWebsiteRepository->setMagentoVersion($magentoVersion);
        $storeWebsiteRepository->setConnection($connection);
        $storeWebsiteRepository->init();

        // initialize the repository that provides tax class query functionality
        $taxClassRepository = new TaxClassRepository();
        $taxClassRepository->setMagentoEdition($magentoEdition);
        $taxClassRepository->setMagentoVersion($magentoVersion);
        $taxClassRepository->setConnection($connection);
        $taxClassRepository->init();

        // initialize the repository that provides link type query functionality
        $linkTypeRepository = new LinkTypeRepository();
        $linkTypeRepository->setMagentoEdition($magentoEdition);
        $linkTypeRepository->setMagentoVersion($magentoVersion);
        $linkTypeRepository->setConnection($connection);
        $linkTypeRepository->init();

        // initialize the import processor
        $processorType = ImportProcessorFactory::getProcessorType();
        $importProcessor = new $processorType();
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
