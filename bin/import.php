<?php

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Importer;
use TechDivision\Import\Services\ProductProcessor;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Utils\PropertyKeys;
use TechDivision\Import\Utils\PdoConnectionUtil;
use TechDivision\Import\Actions\ProductAction;
use TechDivision\Import\Actions\ProductCategoryAction;
use TechDivision\Import\Actions\StockItemAction;
use TechDivision\Import\Actions\StockStatusAction;
use TechDivision\Import\Actions\ProductWebsiteAction;
use TechDivision\Import\Actions\ProductVarcharAction;
use TechDivision\Import\Actions\ProductTextAction;
use TechDivision\Import\Actions\ProductIntAction;
use TechDivision\Import\Actions\ProductDecimalAction;
use TechDivision\Import\Actions\ProductDatetimeAction;
use TechDivision\Import\Actions\ProductRelationAction;
use TechDivision\Import\Actions\ProductSuperAttributeAction;
use TechDivision\Import\Actions\ProductSuperAttributeLabelAction;
use TechDivision\Import\Actions\ProductSuperLinkAction;
use TechDivision\Import\Actions\Processors\ProductPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductCategoryPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductDatetimePersistProcessor;
use TechDivision\Import\Actions\Processors\ProductDecimalPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductIntPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductTextPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductVarcharPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductWebsitePersistProcessor;
use TechDivision\Import\Actions\Processors\ProductRelationPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductSuperAttributePersistProcessor;
use TechDivision\Import\Actions\Processors\ProductSuperAttributeLabelPersistProcessor;
use TechDivision\Import\Actions\Processors\ProductSuperLinkPersistProcessor;
use TechDivision\Import\Actions\Processors\StockItemPersistProcessor;
use TechDivision\Import\Actions\Processors\StockStatusPersistProcessor;
use TechDivision\Import\Repositories\CategoryRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeSetRepository;
use TechDivision\Import\Repositories\StoreRepository;
use TechDivision\Import\Repositories\StoreWebsiteRepository;
use TechDivision\Import\Repositories\TaxClassRepository;

$loader = require dirname(__DIR__) . '/vendor/autoload.php';

$prefix = 'magento-import';
$magentoEdition = 'CE';
$magentoVersion = '2.1.2';
$sourceDir = '/opt/appserver/tmp';
$sourceDateFormat = 'n/d/y, g:i A';

// initialize the PDO connection
$connection = new \PDO('mysql:host=127.0.0.1;dbname=appserver_magento2_ce212', 'magento', 'eraZor');
$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

// initialize the action that provides product category CRUD functionality
$productCategoryPersistProcessor = new ProductCategoryPersistProcessor();
$productCategoryPersistProcessor->setMagentoEdition($magentoEdition);
$productCategoryPersistProcessor->setMagentoVersion($magentoVersion);
$productCategoryPersistProcessor->setConnection($connection);
$productCategoryPersistProcessor->init();
$productCategoryAction = new ProductCategoryAction();
$productCategoryAction->setPersistProcessor($productCategoryPersistProcessor);

// initialize the action that provides product datetime attribute CRUD functionality
$productDatetimePersistProcessor = new ProductDatetimePersistProcessor();
$productDatetimePersistProcessor->setMagentoEdition($magentoEdition);
$productDatetimePersistProcessor->setMagentoVersion($magentoVersion);
$productDatetimePersistProcessor->setConnection($connection);
$productDatetimePersistProcessor->init();
$productDatetimeAction = new ProductDatetimeAction();
$productDatetimeAction->setPersistProcessor($productDatetimePersistProcessor);

// initialize the action that provides product decimal attribute CRUD functionality
$productDecimalPersistProcessor = new ProductDecimalPersistProcessor();
$productDecimalPersistProcessor->setMagentoEdition($magentoEdition);
$productDecimalPersistProcessor->setMagentoVersion($magentoVersion);
$productDecimalPersistProcessor->setConnection($connection);
$productDecimalPersistProcessor->init();
$productDecimalAction = new ProductDecimalAction();
$productDecimalAction->setPersistProcessor($productDecimalPersistProcessor);

// initialize the action that provides product integer attribute CRUD functionality
$productIntPersistProcessor = new ProductIntPersistProcessor();
$productIntPersistProcessor->setMagentoEdition($magentoEdition);
$productIntPersistProcessor->setMagentoVersion($magentoVersion);
$productIntPersistProcessor->setConnection($connection);
$productIntPersistProcessor->init();
$productIntAction = new ProductIntAction();
$productIntAction->setPersistProcessor($productIntPersistProcessor);

// initialize the action that provides product CRUD functionality
$productPersistProcessor = new ProductPersistProcessor();
$productPersistProcessor->setMagentoEdition($magentoEdition);
$productPersistProcessor->setMagentoVersion($magentoVersion);
$productPersistProcessor->setConnection($connection);
$productPersistProcessor->init();
$productAction = new ProductAction();
$productAction->setPersistProcessor($productPersistProcessor);

// initialize the action that provides product text attribute CRUD functionality
$productTextPersistProcessor = new ProductTextPersistProcessor();
$productTextPersistProcessor->setMagentoEdition($magentoEdition);
$productTextPersistProcessor->setMagentoVersion($magentoVersion);
$productTextPersistProcessor->setConnection($connection);
$productTextPersistProcessor->init();
$productTextAction = new ProductTextAction();
$productTextAction->setPersistProcessor($productTextPersistProcessor);

// initialize the action that provides product varchar attribute CRUD functionality
$productVarcharPersistProcessor = new ProductVarcharPersistProcessor();
$productVarcharPersistProcessor->setMagentoEdition($magentoEdition);
$productVarcharPersistProcessor->setMagentoVersion($magentoVersion);
$productVarcharPersistProcessor->setConnection($connection);
$productVarcharPersistProcessor->init();
$productVarcharAction = new ProductVarcharAction();
$productVarcharAction->setPersistProcessor($productVarcharPersistProcessor);

// initialize the action that provides provides product website CRUD functionality
$productWebsitePersistProcessor = new ProductWebsitePersistProcessor();
$productWebsitePersistProcessor->setMagentoEdition($magentoEdition);
$productWebsitePersistProcessor->setMagentoVersion($magentoVersion);
$productWebsitePersistProcessor->setConnection($connection);
$productWebsitePersistProcessor->init();
$productWebsiteAction = new ProductWebsiteAction();
$productWebsiteAction->setPersistProcessor($productWebsitePersistProcessor);

// initialize the action that provides stock item CRUD functionality
$stockItemPersistProcessor = new StockItemPersistProcessor();
$stockItemPersistProcessor->setMagentoEdition($magentoEdition);
$stockItemPersistProcessor->setMagentoVersion($magentoVersion);
$stockItemPersistProcessor->setConnection($connection);
$stockItemPersistProcessor->init();
$stockItemAction = new StockItemAction();
$stockItemAction->setPersistProcessor($stockItemPersistProcessor);

// initialize the action that provides stock status CRUD functionality
$stockStatusPersistProcessor = new StockStatusPersistProcessor();
$stockStatusPersistProcessor->setMagentoEdition($magentoEdition);
$stockStatusPersistProcessor->setMagentoVersion($magentoVersion);
$stockStatusPersistProcessor->setConnection($connection);
$stockStatusPersistProcessor->init();
$stockStatusAction = new StockStatusAction();
$stockStatusAction->setPersistProcessor($stockStatusPersistProcessor);

// initialize the action that provides product relation CRUD functionality
$productRelationPersistProcessor = new ProductRelationPersistProcessor();
$productRelationPersistProcessor->setMagentoEdition($magentoEdition);
$productRelationPersistProcessor->setMagentoVersion($magentoVersion);
$productRelationPersistProcessor->setConnection($connection);
$productRelationPersistProcessor->init();
$productRelationAction = new ProductRelationAction();
$productRelationAction->setPersistProcessor($productRelationPersistProcessor);

// initialize the action that provides product super attribute CRUD functionality
$productSuperAttributePersistProcessor = new ProductSuperAttributePersistProcessor();
$productSuperAttributePersistProcessor->setMagentoEdition($magentoEdition);
$productSuperAttributePersistProcessor->setMagentoVersion($magentoVersion);
$productSuperAttributePersistProcessor->setConnection($connection);
$productSuperAttributePersistProcessor->init();
$productSuperAttributeAction = new ProductSuperAttributeAction();
$productSuperAttributeAction->setPersistProcessor($productSuperAttributePersistProcessor);

// initialize the action that provides product super attribute label CRUD functionality
$productSuperAttributeLabelPersistProcessor = new ProductSuperAttributeLabelPersistProcessor();
$productSuperAttributeLabelPersistProcessor->setMagentoEdition($magentoEdition);
$productSuperAttributeLabelPersistProcessor->setMagentoVersion($magentoVersion);
$productSuperAttributeLabelPersistProcessor->setConnection($connection);
$productSuperAttributeLabelPersistProcessor->init();
$productSuperAttributeLabelAction = new ProductSuperAttributeLabelAction();
$productSuperAttributeLabelAction->setPersistProcessor($productSuperAttributeLabelPersistProcessor);

// initialize the action that provides product super link CRUD functionality
$productSuperLinkPersistProcessor = new ProductSuperLinkPersistProcessor();
$productSuperLinkPersistProcessor->setMagentoEdition($magentoEdition);
$productSuperLinkPersistProcessor->setMagentoVersion($magentoVersion);
$productSuperLinkPersistProcessor->setConnection($connection);
$productSuperLinkPersistProcessor->init();
$productSuperLinkAction = new ProductSuperLinkAction();
$productSuperLinkAction->setPersistProcessor($productSuperLinkPersistProcessor);

// initialize the repository that provides category query functionality
$categoryRepository = new CategoryRepository();
$categoryRepository->setMagentoEdition($magentoEdition);
$categoryRepository->setMagentoVersion($magentoVersion);
$categoryRepository->setConnection($connection);
$categoryRepository->init();

// initialize the repository that provides EAV attribute option value query functionality
$eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
$eavAttributeOptionValueRepository->setMagentoEdition($magentoEdition);
$eavAttributeOptionValueRepository->setMagentoVersion($magentoVersion);
$eavAttributeOptionValueRepository->setConnection($connection);
$eavAttributeOptionValueRepository->init();

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

// initialize the product processor
$productProcessor = new ProductProcessor();
$productProcessor->setConnection($connection);
$productProcessor->setProductCategoryAction($productCategoryAction);
$productProcessor->setProductDatetimeAction($productDatetimeAction);
$productProcessor->setProductDecimalAction($productDecimalAction);
$productProcessor->setProductIntAction($productIntAction);
$productProcessor->setProductAction($productAction);
$productProcessor->setProductTextAction($productTextAction);
$productProcessor->setProductVarcharAction($productVarcharAction);
$productProcessor->setProductWebsiteAction($productWebsiteAction);
$productProcessor->setProductRelationAction($productRelationAction);
$productProcessor->setProductSuperAttributeAction($productSuperAttributeAction);
$productProcessor->setProductSuperAttributeLabelAction($productSuperAttributeLabelAction);
$productProcessor->setProductSuperLinkAction($productSuperLinkAction);
$productProcessor->setStockItemAction($stockItemAction);
$productProcessor->setStockStatusAction($stockStatusAction);
$productProcessor->setCategoryRepository($categoryRepository);
$productProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
$productProcessor->setEavAttributeRepository($eavAttributeRepository);
$productProcessor->setEavAttributeSetRepository($eavAttributeSetRepository);
$productProcessor->setStoreRepository($storeRepository);
$productProcessor->setStoreWebsiteRepository($storeWebsiteRepository);
$productProcessor->setTaxClassRepository($taxClassRepository);

$registryProcessor = new RegistryProcessor();

$systemLogger = new Logger('techdivision/import');
$systemLogger->pushHandler(new ErrorLogHandler());

$importer = new Importer();

// initialize the importer
$importer->setSourceDir($sourceDir);
$importer->setSourceDateFormat($sourceDateFormat);
$importer->setSystemLogger($systemLogger);
$importer->setProductProcessor($productProcessor);
$importer->setRegistryProcessor($registryProcessor);

$importer->import($prefix);
