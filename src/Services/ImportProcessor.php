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

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImportProcessor implements ImportProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \PDO
     */
    protected $connection;

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
     * The repository to access link types.
     *
     * @var \TechDivision\Import\Repositories\LinkTypeRepository
     */
    protected $linkTypeRepository;

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
     * Set's the repository to access link types.
     *
     * @param \TechDivision\Import\Repositories\LinkTypeRepository $linkTypeRepository The repository to access link types
     *
     * @return void
     */
    public function setLinkTypeRepository($linkTypeRepository)
    {
        $this->linkTypeRepository = $linkTypeRepository;
    }

    /**
     * Return's the repository to access categories.
     *
     * @return \TechDivision\Import\Repositories\CategoryRepository The repository instance
     */
    public function getLinkTypeRepository()
    {
        return $this->linkTypeRepository;
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
     * Return's an array with all available link types.
     *
     * @return array The available link types
     */
    public function getLinkTypes()
    {
        return $this->getLinkTypeRepository()->findAll();
    }
}
