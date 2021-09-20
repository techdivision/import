<?php

/**
 * TechDivision\Import\Utils\ColumnValuesUtilTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Utils;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Loaders\LoaderInterface;

/**
 * Test class for the column values utility.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnValuesUtilTest extends TestCase
{

    /**
     * The utility we want to test.
     *
     * @var \TechDivision\Import\Utils\ColumnValuesUtilInterface
     */
    protected $columnValuesUtil;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // mock the array with the column names
        $columnNames = array(
            MemberNames::STORE_ID,
            MemberNames::ATTRIBUTE_ID,
            MemberNames::VALUE
        );

        // mock the column name loader
        $mockColumnNameLoader = $this->getMockBuilder(LoaderInterface::class)
            ->setMethods(get_class_methods(LoaderInterface::class))
            ->getMock();
        $mockColumnNameLoader->expects($this->any())->method('load')->willReturn($columnNames);

        // mock the table prefix utility
        $mockTablePrefixUtil = $this->getMockBuilder(TablePrefixUtilInterface::class)
            ->setMethods(get_class_methods(TablePrefixUtilInterface::class))
            ->getMock();
        $mockTablePrefixUtil->expects($this->any())->method('getPrefixedTableName')->willReturn('catalog_product_entity_varchar');

        // initialize the utility we want to test
        $this->columnValuesUtil = new ColumnValuesUtil($mockColumnNameLoader, $mockTablePrefixUtil);
    }

    /**
     * Test's if the compile() method returns the properly compiled SQL.
     *
     * @return void
     */
    public function testCompile()
    {

        // create the statement that has to be compiled
        $statement = 'UPDATE catalog_product_entity_varchar
                 SET ${column-values:catalog_product_entity_varchar}
               WHERE entity_id = :entity_id';

        // create the SQL for the prepared statment we expect after compiling
        $expectedStatement = 'UPDATE catalog_product_entity_varchar
                 SET store_id=:store_id,attribute_id=:attribute_id,value=:value
               WHERE entity_id = :entity_id';

        // assert that the statement has been compiled to a proper SQL
        $this->assertSame($expectedStatement, $this->columnValuesUtil->compile($statement));
    }
}
