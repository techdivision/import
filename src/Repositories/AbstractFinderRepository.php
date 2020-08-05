<?php

/**
 * TechDivision\Import\Repositories\AbstractFinderRepository
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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\Finders\FinderInterface;
use TechDivision\Import\Repositories\Finders\FinderFactoryInterface;

/**
 * Abstract repository implementation with finder support.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractFinderRepository extends AbstractRepository implements FinderAwareRepositoryInterface
{

    /**
     * The finder factory.
     *
     * @var \TechDivision\Import\Repositories\Finders\FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * The array with the initialized finders.
     *
     * @var array
     */
    protected $finders = array();

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     * @param \TechDivision\Import\Repositories\Finders\FinderFactoryInterface  $finderFactory          The finder factory instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        FinderFactoryInterface $finderFactory
    ) {

        // set the finder factory
        $this->finderFactory = $finderFactory;

        // pass the connection the SQL statement repository to the parent class
        parent::__construct($connection, $sqlStatementRepository);
    }

    /**
     * Add the initialize finder to the repository.
     *
     * @param \TechDivision\Import\Repositories\Finders\FinderInterface $finder The finder instance to add
     *
     * @return void
     */
    public function addFinder(FinderInterface $finder)
    {
        $this->finders[$finder->getKey()] = $finder;
    }

    /**
     * Return's the finder instance with the passed key.
     *
     * @param string $key The key of the finder to return
     *
     * @return \TechDivision\Import\Repositories\Finders\FinderInterface The finder instance
     * @throws \InvalidArgumentException Is thrown if the finder with the passed key is not available
     */
    public function getFinder($key)
    {

        // query whether or not the finder is available
        if (isset($this->finders[$key])) {
            return $this->finders[$key];
        }

        // throw an exception, if not
        throw new \InvalidArgumentException(sprintf('Finder "%s" has not ebeen registered', $key));
    }
}
