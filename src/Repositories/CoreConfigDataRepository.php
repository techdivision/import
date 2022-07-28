<?php

/**
 * TechDivision\Import\Repositories\CoreConfigDataRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Dbal\Collection\Repositories\AbstractFinderRepository;
use TechDivision\Import\Dbal\Repositories\Finders\FinderFactoryInterface;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface;

/**
 * Repository implementation to load the Magento 2 configuration data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CoreConfigDataRepository extends AbstractFinderRepository implements CoreConfigDataRepositoryInterface
{

    /**
     * The UID generator for the core config data.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    protected $coreConfigDataUidGenerator;

    /**
     * The statement to load the configuration.
     *
     * @var \PDOStatement
     */
    protected $coreConfigDataStmt;

    /**
     * @var array
     */
    protected $coreConfigData = array();

    /**
     * @param GeneratorInterface              $coreConfigDataUidGenerator The coreConfigDataUidGenerator factory instance
     * @param ConnectionInterface             $connection                 The connection factory instance
     * @param SqlStatementRepositoryInterface $sqlStatementRepository     The SQL repository instance
     * @param FinderFactoryInterface          $finderFactory              The finder factory instance
     */
    public function __construct(
        GeneratorInterface $coreConfigDataUidGenerator,
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        FinderFactoryInterface $finderFactory
    ) {
        parent::__construct($connection, $sqlStatementRepository, $finderFactory);
        $this->coreConfigDataUidGenerator = $coreConfigDataUidGenerator;
    }

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
        // initialize the prepared statements
        $this->coreConfigDataStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CORE_CONFIG_DATA));
        $this->addFinder($this->finderFactory->createFinder($this, SqlStatementKeys::CORE_CONFIG_DATA));
    }

    /**
     * Return's an array with the Magento 2 configuration.
     *
     * @return array The configuration
     */
    public function findAll()
    {
        // execute the prepared statement
        $this->coreConfigDataStmt->execute();

        // load the available core config data from database
        $availableCoreConfigData = $this->coreConfigDataStmt->fetchAll(\PDO::FETCH_ASSOC);

        // load the available core config data from magento instance
        $availableCoreConfigApiData = $this->getFinder(SqlStatementKeys::CORE_CONFIG_DATA)->find();

        $availableCoreConfigData = array_merge($availableCoreConfigData, $availableCoreConfigApiData);

        // Set core config data from api and db
        $this->setCoreConfigData($availableCoreConfigData);

        // return array with the configuration data
        return $this->coreConfigData;
    }

    /**
     * create the array with the resolved category path as keys
     *
     * @param array $availableCoreConfigData the available Core ConfigData
     *
     * @return void
     */
    protected function setCoreConfigData($availableCoreConfigData)
    {
        foreach ($availableCoreConfigData as $coreConfigData) {
            // prepare the unique identifier
            $uniqueIdentifier = $this->coreConfigDataUidGenerator->generate($coreConfigData);
            // append the config data value with the unique identifier
            $this->coreConfigData[$uniqueIdentifier] = $coreConfigData;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKeyName()
    {
        return \TechDivision\Import\Utils\MemberNames::PATH;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return '';
    }
}
