<?php

/**
 * TechDivision\Import\Utils\SqlStatementRepositoryTest
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

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\TablePrefixUtil;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatementRepositoryTest extends TestCase
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
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // initialize the mock compiler instance
        $mockCompiler = $this->getMockBuilder(TablePrefixUtil::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getPrefixedTableName'))
            ->getMock();
        $mockCompiler->expects($this->any())
            ->method('getPrefixedTableName')
            ->willReturnArgument(0);

        // initialize the traversable with the mock compilers
        $compilers = new \ArrayObject();
        $compilers->append($mockCompiler);

        // initialize the SQL statement repository
        $this->sqlStatementRepository = new SqlStatementRepository($compilers);
    }

    /**
     * Test's if the find() method returns the SQL for the passed key.
     *
     * @return void
     */
    public function testFindWithSuccess()
    {

        // query whether or not the SQL statement can be loaded
        $this->assertEquals('SELECT * FROM core_config_data', $this->sqlStatementRepository->load(SqlStatementKeys::CORE_CONFIG_DATA));
    }

    /**
     * Test's if the find() method throws an exception if no SQL for the passed key is not available.
     *
     * @return void
     */
    public function testFindWithException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Can't find SQL statement with ID a.id.that.is.not.available");

        $this->sqlStatementRepository->load('a.id.that.is.not.available');
    }
}
