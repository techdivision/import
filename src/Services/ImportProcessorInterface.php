<?php

/**
 * TechDivision\Import\Services\ImportProcessorInterface
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
 * Interface for a import procesor implemenation, that provides functionality to
 * load the global data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ImportProcessorInterface
{

    /**
     * Return's the connection.
     *
     * @return \TechDivision\Import\Connection\ConnectionInterface The connection instance
     */
    public function getConnection();

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction();

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit();

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
    public function rollBack();

    /**
     * Return's the category assembler.
     *
     * @return \TechDivision\Import\Assembler\CategoryAssemblerInterface The category assembler instance
     */
    public function getCategoryAssembler();

    /**
     * Return's the repository to access categories.
     *
     * @return \TechDivision\Import\Repositories\CategoryRepositoryInterface The repository instance
     */
    public function getCategoryRepository();

    /**
     * Return's the repository to access category varchar values.
     *
     * @return \TechDivision\Import\Repositories\CategoryVarcharRepositoryInterface The repository instance
     */
    public function getCategoryVarcharRepository();

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository();

    /**
     * Return's the repository to access EAV attribute sets.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeSetRepositoryInterface The repository instance
     */
    public function getEavAttributeSetRepository();

    /**
     * Return's the repository to access EAV attribute groups.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeGroupRepositoryInterface The repository instance
     */
    public function getEavAttributeGroupRepository();

    /**
     * Return's the repository to access stores.
     *
     * @return \TechDivision\Import\Repositories\StoreRepositoryInterface The repository instance
     */
    public function getStoreRepository();

    /**
     * Return's the repository to access store websites.
     *
     * @return \TechDivision\Import\Repositories\StoreWebsiteRepositoryInterface The repository instance
     */
    public function getStoreWebsiteRepository();

    /**
     * Return's the repository to access tax classes.
     *
     * @return \TechDivision\Import\Repositories\TaxClassRepositoryInterface The repository instance
     */
    public function getTaxClassRepository();

    /**
     * Return's the repository to access link types.
     *
     * @return \TechDivision\Import\Repositories\LinkTypeRepositoryInterface The repository instance
     */
    public function getLinkTypeRepository();

    /**
     * Return's the repository to access link attributes.
     *
     * @return \TechDivision\Import\Repositories\LinkAttributeRepositoryInterface The repository instance
     */
    public function getLinkAttributeRepository();

    /**
     * Return's the repository to access link types.
     *
     * @return \TechDivision\Import\Repositories\ImageTypeRepositoryInterface The repository instance
     */
    public function getImageTypeRepository();

    /**
     * Return's the repository to access the Magento 2 configuration.
     *
     * @return \TechDivision\Import\Repositories\CoreConfigDataRepositoryInterface The repository instance
     */
    public function getCoreConfigDataRepository();

    /**
     * Return's the action with the store CRUD methods.
     *
     * @return \TechDivision\Import\Actions\StoreActionInterface The action instance
     */
    public function getStoreAction();

    /**
     * Return's the action with the store group CRUD methods.
     *
     * @return \TechDivision\Import\Actions\StoreGroupActionInterface The action instance
     */
    public function getStoreGroupAction();

    /**
     * Return's the action with the store website CRUD methods.
     *
     * @return \TechDivision\Import\Actions\StoreWebsiteActionInterface The action instance
     */
    public function getStoreWebsiteAction();

    /**
     * Return's the EAV attribute set with the passed ID.
     *
     * @param integer $id The ID of the EAV attribute set to load
     *
     * @return array The EAV attribute set
     */
    public function getEavAttributeSet($id);

    /**
     * Return's the attribute sets for the passed entity type ID.
     *
     * @param mixed $entityTypeId The entity type ID to return the attribute sets for
     *
     * @return array|boolean The attribute sets for the passed entity type ID
     */
    public function getEavAttributeSetsByEntityTypeId($entityTypeId);

    /**
     * Return's the attribute groups for the passed attribute set ID, whereas the array
     * is prepared with the attribute group names as keys.
     *
     * @param mixed $attributeSetId The EAV attribute set ID to return the attribute groups for
     *
     * @return array|boolean The EAV attribute groups for the passed attribute ID
     */
    public function getEavAttributeGroupsByAttributeSetId($attributeSetId);

    /**
     * Return's an array with the EAV attributes for the passed entity type ID and attribute set name.
     *
     * @param integer $entityTypeId     The entity type ID of the EAV attributes to return
     * @param string  $attributeSetName The attribute set name of the EAV attributes to return
     *
     * @return array The
     */
    public function getEavAttributesByEntityTypeIdAndAttributeSetName($entityTypeId, $attributeSetName);

    /**
     * Return's an array with the available EAV attributes for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with all available EAV attributes
     */
    public function getEavAttributesByOptionValueAndStoreId($optionValue, $storeId);

    /**
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function getEavAttributesByIsUserDefined($isUserDefined = 1);

    /**
     * Return's an array with the available EAV attributes for the passed is entity type and
     * user defined flag.
     *
     * @param integer $entityTypeId  The entity type ID of the EAV attributes to return
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed entity type and user defined flag
     */
    public function getEavAttributesByEntityTypeIdAndIsUserDefined($entityTypeId, $isUserDefined = 1);

    /**
     * Return's an array with the availabe EAV attributes for the passed entity type.
     *
     * @param integer $entityTypeId The entity type ID of the EAV attributes to return
     *
     * @return array The array with the EAV attributes matching the passed entity type
     */
    public function getEavAttributesByEntityTypeId($entityTypeId);

    /**
     * Return's an array with all available EAV entity types with the entity type code as key.
     *
     * @return array The available link types
     */
    public function getEavEntityTypes();

    /**
     * Return's an array with the available stores.
     *
     * @return array The array with the available stores
     */
    public function getStores();

    /**
     * Return's the default store.
     *
     * @return array The default store
     */
    public function getDefaultStore();

    /**
     * Return's an array with the available store websites.
     *
     * @return array The array with the available store websites
     */
    public function getStoreWebsites();

    /**
     * Return's an array with the available tax classes.
     *
     * @return array The array with the available tax classes
     */
    public function getTaxClasses();

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function getCategories();

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function getRootCategories();

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity IDs.
     *
     * @param array $entityIds The array with the category IDs
     *
     * @return mixed The category varchar values
     */
    public function getCategoryVarcharsByEntityIds(array $entityIds);

    /**
     * Return's an array with all available link types.
     *
     * @return array The available link types
     */
    public function getLinkTypes();

    /**
     * Return's an array with all available link attributes.
     *
     * @return array The available link attributes
     */
    public function getLinkAttributes();

    /**
     * Return's an array with all available image types.
     *
     * @return array The available image types
     */
    public function getImageTypes();

    /**
     * Return's an array with the Magento 2 configuration.
     *
     * @return array The Magento 2 configuration
     */
    public function getCoreConfigData();

    /**
     * Persist's the passed store.
     *
     * @param array $store The store to persist
     *
     * @return void
     */
    public function persistStore(array $store);

    /**
     * Persist's the passed store group.
     *
     * @param array $storeGroup The store group to persist
     *
     * @return void
     */
    public function persistStoreGroup(array $storeGroup);

    /**
     * Persist's the passed store website.
     *
     * @param array $storeWebsite The store website to persist
     *
     * @return void
     */
    public function persistStoreWebsite(array $storeWebsite);

    /**
     * Returns the array with the global data necessary for the
     * import process.
     *
     * @return array The array with the global data
     */
    public function getGlobalData();
}
