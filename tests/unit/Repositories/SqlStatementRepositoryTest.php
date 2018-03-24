<?php

/**
 * TechDivision\Import\Utils\SqlStatementRepositoryTest
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

use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatementRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Repositories\SqlStatementRepository
     */
    protected $sqlStatementRepository;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->sqlStatementRepository = new SqlStatementRepository();
    }

    /**
     * Test's if the find() method returns the SQL for the passed key.
     *
     * @return void
     */
    public function testFindWithSuccess()
    {

        // query whether or not the SQL statement can be loade
        $this->assertEquals('SELECT * FROM core_config_data', $this->sqlStatementRepository->load(SqlStatementKeys::CORE_CONFIG_DATA));
    }

    /**
     * Test's if the find() method throws an exception if no SQL for the passed key is not available.
     *
     * @return void
     * @expectedException \Exception
     * @expectedExceptionMessage Can't find SQL statement with ID a.id.that.is.not.available
     */
    public function testFindWithException()
    {
        $this->sqlStatementRepository->load('a.id.that.is.not.available');
    }
}
