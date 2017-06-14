<?php

/**
 * TechDivision\Import\Utils\SqlStatementsTest
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

namespace TechDivision\Import\Utils;

/**
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SqlStatementsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Utils\SqlStatements
     */
    protected $sqlStatements;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->sqlStatements = new SqlStatements();
    }

    /**
     * Test's if the find() method returns the SQL for the passed key.
     *
     * @return void
     */
    public function testFindWithSuccess()
    {

        // load the utility class name
        $utilityClassName = get_class($this->sqlStatements);

        // query whether or not the SQL statement can be loade
        $this->assertEquals('SELECT * FROM core_config_data', $this->sqlStatements->find($utilityClassName::CORE_CONFIG_DATA));
    }

    /**
     * Test's if the find() method throws an exception if no SQL for the passed key is not available.
     *
     * @return void
     * @expectedException \Exception
     * @expectedExceptionMessage Can't find SQL statement with key a.key.that.is.not.available
     */
    public function testFindWithException()
    {
        $this->sqlStatements->find('a.key.that.is.not.available');
    }
}
