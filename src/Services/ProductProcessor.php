<?php

/**
 * TechDivision\Import\Services\ProductProcessor
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

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductProcessor implements ProductProcessorInterface
{

    /**
     * The Doctrine EntityManager instance.
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * The action for product CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductAction
     */
    protected $productAction;

    /**
     * The action for product varchar attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductVarcharAction
     */
    protected $productVarcharAction;

    /**
     * The action for product text attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductTextAction
     */
    protected $productTextAction;

    /**
     * The action for product int attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductTextAction
     */
    protected $productIntAction;

    /**
     * The action for product decimal attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductDecimalAction
     */
    protected $productDecimalAction;

    /**
     * The action for product datetime attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductDatetiemAction
     */
    protected $productDatetimeAction;

    /**
     * The action for product website CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductWebsiteAction
     */
    protected $productWebsiteAction;

    /**
     * The action for product category CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductCategoryAction
     */
    protected $productCategoryAction;

    /**
     * The action for stock item CRUD methods.
     *
     * @var \TechDivision\Import\Actions\StockItemAction
     */
    protected $stockItemAction;

    /**
     * The action for stock status CRUD methods.
     *
     * @var \TechDivision\Import\Actions\StockStatusAction
     */
    protected $stockStatusAction;

    /**
     * The action for product relation CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductRelationAction
     */
    protected $productRelationAction;

    /**
     * The action for product super attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductSuperAttributeAction
     */
    protected $productSuperAttributeAction;

    /**
     * The action for product super attribute label CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductSuperAttributeLabelAction
     */
    protected $productSuperAttributeLabelAction;

    /**
     * The action for product super link CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ProductSuperLinkAction
     */
    protected $productSuperLinkAction;

    /**
     * The repository to access categories.
     *
     * @var \TechDivision\Import\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * The repository to access category varchar values.
     *
     * @var \TechDivision\Import\Repositories\CategoryVarcharRepository
     */
    protected $categoryVarcharRepository;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeOptionValueRepository
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * The repository to access EAV attribute set.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeSetRepository
     */
    protected $eavAttributeSetRepository;

    /**
     * The repository to access stores.
     *
     * @var \TechDivision\Import\Repositories\StoreRepository
     */
    protected $storeRepository;

    /**
     * The repository to access store websites.
     *
     * @var \TechDivision\Import\Repositories\StoreWebsiteRepository
     */
    protected $storeWebsiteRepository;

    /**
     * The repository to access tax classes.
     *
     * @var \TechDivision\Import\Repositories\TaxClassRepository
     */
    protected $taxClassRepository;

    /**
     * Set's the passed connection.
     *
     * @param \PDO $connection The connection to set
     *
     * @return void
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \PDO The connection instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rolls back the current transaction, as initiated by ProductProcessor::beginTransaction().
     *
     * If the database was set to autocommit mode, this function will restore autocommit mode after it has
     * rolled back the transaction.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition
     * language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit
     * COMMIT will prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.rollback.php
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Set's the action with the product CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductAction $productAction The action with the product CRUD methods
     *
     * @return void
     */
    public function setProductAction($productAction)
    {
        $this->productAction = $productAction;
    }

    /**
     * Return's the action with the product CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductAction The action instance
     */
    public function getProductAction()
    {
        return $this->productAction;
    }

    /**
     * Set's the action with the product varchar attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductVarcharAction $productVarcharAction The action with the product varchar attriute CRUD methods
     *
     * @return void
     */
    public function setProductVarcharAction($productVarcharAction)
    {
        $this->productVarcharAction = $productVarcharAction;
    }

    /**
     * Return's the action with the product varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductVarcharAction The action instance
     */
    public function getProductVarcharAction()
    {
        return $this->productVarcharAction;
    }

    /**
     * Set's the action with the product text attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductTextAction $productTextAction The action with the product text attriute CRUD methods
     *
     * @return void
     */
    public function setProductTextAction($productTextAction)
    {
        $this->productTextAction = $productTextAction;
    }

    /**
     * Return's the action with the product text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductTextAction The action instance
     */
    public function getProductTextAction()
    {
        return $this->productTextAction;
    }

    /**
     * Set's the action with the product int attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductIntAction $productIntAction The action with the product int attriute CRUD methods
     *
     * @return void
     */
    public function setProductIntAction($productIntAction)
    {
        $this->productIntAction = $productIntAction;
    }

    /**
     * Return's the action with the product int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductIntAction The action instance
     */
    public function getProductIntAction()
    {
        return $this->productIntAction;
    }

    /**
     * Set's the action with the product decimal attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductDecimalAction $productDecimalAction The action with the product decimal attriute CRUD methods
     *
     * @return void
     */
    public function setProductDecimalAction($productDecimalAction)
    {
        $this->productDecimalAction = $productDecimalAction;
    }

    /**
     * Return's the action with the product decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductDecimalAction The action instance
     */
    public function getProductDecimalAction()
    {
        return $this->productDecimalAction;
    }

    /**
     * Set's the action with the product datetime attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductDatetimeAction $productDatetimeAction The action with the product datetime attriute CRUD methods
     *
     * @return void
     */
    public function setProductDatetimeAction($productDatetimeAction)
    {
        $this->productDatetimeAction = $productDatetimeAction;
    }

    /**
     * Return's the action with the product datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductDatetimeAction The action instance
     */
    public function getProductDatetimeAction()
    {
        return $this->productDatetimeAction;
    }

    /**
     * Set's the action with the product website CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductWebsiteAction $productWebsiteAction The action with the product website CRUD methods
     *
     * @return void
     */
    public function setProductWebsiteAction($productWebsiteAction)
    {
        $this->productWebsiteAction = $productWebsiteAction;
    }

    /**
     * Return's the action with the product website CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductWebsiteAction The action instance
     */
    public function getProductWebsiteAction()
    {
        return $this->productWebsiteAction;
    }

    /**
     * Set's the action with the product category CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductCategoryAction $productCategoryAction The action with the product category CRUD methods
     *
     * @return void
     */
    public function setProductCategoryAction($productCategoryAction)
    {
        $this->productCategoryAction = $productCategoryAction;
    }

    /**
     * Return's the action with the product category CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductCategoryAction The action instance
     */
    public function getProductCategoryAction()
    {
        return $this->productCategoryAction;
    }

    /**
     * Set's the action with the stock item CRUD methods.
     *
     * @param \TechDivision\Import\Actions\StockItemAction $stockItemAction The action with the stock item CRUD methods
     *
     * @return void
     */
    public function setStockItemAction($stockItemAction)
    {
        $this->stockItemAction = $stockItemAction;
    }

    /**
     * Return's the action with the stock item CRUD methods.
     *
     * @return \TechDivision\Import\Actions\StockItemAction The action instance
     */
    public function getStockItemAction()
    {
        return $this->stockItemAction;
    }

    /**
     * Set's the action with the stock status CRUD methods.
     *
     * @param \TechDivision\Import\Actions\StockStatusAction $stockStatusAction The action with the stock status CRUD methods
     *
     * @return void
     */
    public function setStockStatusAction($stockStatusAction)
    {
        $this->stockStatusAction = $stockStatusAction;
    }

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Actions\StockStatusAction The action instance
     */
    public function getStockStatusAction()
    {
        return $this->stockStatusAction;
    }

    /**
     * Set's the action with the product relation CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductRelationAction $productRelationAction The action with the product relation CRUD methods
     *
     * @return void
     */
    public function setProductRelationAction($productRelationAction)
    {
        $this->productRelationAction = $productRelationAction;
    }

    /**
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductRelationAction The action instance
     */
    public function getProductRelationAction()
    {
        return $this->productRelationAction;
    }

    /**
     * Set's the action with the product super attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductSuperAttributeAction $productSuperAttributeAction The action with the product super attribute CRUD methods
     *
     * @return void
     */
    public function setProductSuperAttributeAction($productSuperAttributeAction)
    {
        $this->productSuperAttributeAction = $productSuperAttributeAction;
    }

    /**
     * Return's the action with the product super attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductSuperAttributeAction The action instance
     */
    public function getProductSuperAttributeAction()
    {
        return $this->productSuperAttributeAction;
    }

    /**
     * Set's the action with the product super attribute label CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductSuperAttributeLabelAction $productSuperAttributeLabelAction The action with the product super attribute label CRUD methods
     *
     * @return void
     */
    public function setProductSuperAttributeLabelAction($productSuperAttributeLabelAction)
    {
        $this->productSuperAttributeLabelAction = $productSuperAttributeLabelAction;
    }

    /**
     * Return's the action with the product super attribute label CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductSuperAttributeLabelAction The action instance
     */
    public function getProductSuperAttributeLabelAction()
    {
        return $this->productSuperAttributeLabelAction;
    }

    /**
     * Set's the action with the product super link CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ProductSuperLinkAction $productSuperLinkAction The action with the product super link CRUD methods
     *
     * @return void
     */
    public function setProductSuperLinkAction($productSuperLinkAction)
    {
        $this->productSuperLinkAction = $productSuperLinkAction;
    }

    /**
     * Return's the action with the product super link CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ProductSuperLinkAction The action instance
     */
    public function getProductSuperLinkAction()
    {
        return $this->productSuperLinkAction;
    }

    /**
     * Set's the repository to access categories.
     *
     * @param \TechDivision\Import\Repositories\CategoryRepository $categoryRepository The repository to access categories
     *
     * @return void
     */
    public function setCategoryRepository($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Return's the repository to access categories.
     *
     * @return \TechDivision\Import\Repositories\CategoryRepository The repository instance
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * Return's the repository to access category varchar values.
     *
     * @param \TechDivision\Import\Repositories\CategoryVarcharRepository $categoryVarcharRepository The repository instance
     *
     * @return void
     */
    public function setCategoryVarcharRepository($categoryVarcharRepository)
    {
        $this->categoryVarcharRepository = $categoryVarcharRepository;
    }

    /**
     * Return's the repository to access category varchar values.
     *
     * @return \TechDivision\Import\Repositories\CategoryVarcharRepository The repository instance
     */
    public function getCategoryVarcharRepository()
    {
        return $this->categoryVarcharRepository;
    }

    /**
     * Set's the repository to access EAV attribute option values.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepository $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepository The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the repository to access EAV attributes.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeRepository $eavAttributeRepository The repository to access EAV attributes
     *
     * @return void
     */
    public function setEavAttributeRepository($eavAttributeRepository)
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepository The repository instance
     */
    public function getEavAttributeRepository()
    {
        return $this->eavAttributeRepository;
    }

    /**
     * Set's the repository to access EAV attribute sets.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeSetRepository $eavAttributeSetRepository The repository the access EAV attribute sets
     *
     * @return void
     */
    public function setEavAttributeSetRepository($eavAttributeSetRepository)
    {
        $this->eavAttributeSetRepository = $eavAttributeSetRepository;
    }

    /**
     * Return's the repository to access EAV attribute sets.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeSetRepository The repository instance
     */
    public function getEavAttributeSetRepository()
    {
        return $this->eavAttributeSetRepository;
    }

    /**
     * Set's the repository to access stores.
     *
     * @param \TechDivision\Import\Repositories\StoreRepository $storeRepository The repository the access stores
     *
     * @return void
     */
    public function setStoreRepository($storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * Return's the repository to access stores.
     *
     * @return \TechDivision\Import\Repositories\StoreRepository The repository instance
     */
    public function getStoreRepository()
    {
        return $this->storeRepository;
    }

    /**
     * Set's the repository to access store websites.
     *
     * @param \TechDivision\Import\Repositories\StoreWebsiteRepository $storeWebsiteRepository The repository the access store websites
     *
     * @return void
     */
    public function setStoreWebsiteRepository($storeWebsiteRepository)
    {
        $this->storeWebsiteRepository = $storeWebsiteRepository;
    }

    /**
     * Return's the repository to access store websites.
     *
     * @return \TechDivision\Import\Repositories\StoreWebsiteRepository The repository instance
     */
    public function getStoreWebsiteRepository()
    {
        return $this->storeWebsiteRepository;
    }

    /**
     * Set's the repository to access tax classes.
     *
     * @param \TechDivision\Import\Repositories\TaxClassRepository $taxClassRepository The repository the access stores
     *
     * @return void
     */
    public function setTaxClassRepository($taxClassRepository)
    {
        $this->taxClassRepository = $taxClassRepository;
    }

    /**
     * Return's the repository to access tax classes.
     *
     * @return \TechDivision\Import\Repositories\TaxClassRepository The repository instance
     */
    public function getTaxClassRepository()
    {
        return $this->taxClassRepository;
    }

    /**
     * Persist's the passed product data and return's the ID.
     *
     * @param array $product The product data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProduct($product)
    {
        return $this->getProductAction()->persist($product);
    }

    /**
     * Persist's the passed product varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute)
    {
        $this->getProductVarcharAction()->persist($attribute);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute)
    {
        $this->getProductIntAction()->persist($attribute);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute)
    {
        $this->getProductDecimalAction()->persist($attribute);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute)
    {
        $this->getProductDatetimeAction()->persist($attribute);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute)
    {
        $this->getProductTextAction()->persist($attribute);
    }

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array $productWebsite The product website data to persist
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite)
    {
        $this->getProductWebsiteAction()->persist($productWebsite);
    }

    /**
     * Persist's the passed product category data and return's the ID.
     *
     * @param array $productWebsite The product category data to persist
     *
     * @return void
     */
    public function persistProductCategory($productCategory)
    {
        $this->getProductCategoryAction()->persist($productCategory);
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    public function persistStockItem($stockItem)
    {
        $this->getStockItemAction()->persist($stockItem);
    }

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array $stockItem The stock status data to persist
     *
     * @return void
     */
    public function persistStockStatus($stockStatus)
    {
        $this->getStockStatusAction()->persist($stockStatus);
    }

    /**
     * Return's the EAV attribute set with the passed ID.
     *
     * @param integer $id The ID of the EAV attribute set to load
     *
     * @return array The EAV attribute set
     */
    public function getEavAttributeSet($id)
    {
        return $this->getEavAttributeSetRepository()->load($id);
    }

    /**
     * Return's the attribute sets for the passed entity type ID.
     *
     * @param mixed $entityTypeId The entity type ID to return the attribute sets for
     *
     * @return array|boolean The attribute sets for the passed entity type ID
     */
    public function getEavAttributeSetsByEntityTypeId($entityTypeId)
    {
        return $this->getEavAttributeSetRepository()->findAllByEntityTypeId($entityTypeId);
    }

    /**
     * Return's an array with the EAV attributes for the passed entity type ID and attribute set name.
     *
     * @param integer $entityTypeId     The entity type ID of the EAV attributes to return
     * @param string  $attributeSetName The attribute set name of the EAV attributes to return
     *
     * @return array The
     */
    public function getEavAttributesByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName)
    {
        return $this->getEavAttributeRepository()->findAllByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName);
    }

    /**
     * Return's an array with the available EAV attributes for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with all available EAV attributes
     */
    public function getEavAttributesByOptionValueAndStoreId($optionValue, $storeId)
    {
        return $this->getEavAttributeRepository()->findAllByOptionValueAndStoreId($optionValue, $storeId);
    }

    /**
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function getEavAttributeByOptionValueAndStoreId($optionValue, $storeId)
    {
        return $this->getEavAttributeRepository()->findOneByOptionValueAndStoreId($optionValue, $storeId);
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getEavAttributeOptionValueRepository()->findEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }

    /**
     * Return's an array with the available stores.
     *
     * @return array The array with the available stores
     */
    public function getStores()
    {
        return $this->getStoreRepository()->findAll();
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
     * Return's an array with the available tax classes.
     *
     * @return array The array with the available tax classes
     */
    public function getTaxClasses()
    {
        return $this->getTaxClassRepository()->findAll();
    }

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function getCategories()
    {
        return $this->getCategoryRepository()->findAll();
    }

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity IDs.
     *
     * @param array $entityIds The array with the category IDs
     *
     * @return mixed The category varchar values
     */
    public function getCategoryVarcharsByEntityIds(array $entityIds)
    {
        return $this->getCategoryVarcharRepository()->findAllByEntityIds($entityIds);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $product The product relation data to persist
     *
     * @return void
     */
    public function persistProductRelation($productRelation)
    {
        return $this->getProductRelationAction()->persist($productRelation);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink)
    {
        return $this->getProductSuperLinkAction()->persist($productSuperLink);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return void
     */
    public function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getProductSuperAttributeAction()->persist($productSuperAttribute);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductSuperAttributeLabelAction()->persist($productSuperAttributeLabel);
    }
}
