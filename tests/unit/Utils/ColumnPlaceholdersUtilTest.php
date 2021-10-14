<?php

/**
 * TechDivision\Import\Utils\ColumnPlaceholdersUtilTest
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
 * Test class for the column placeholders utility.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnPlaceholdersUtilTest extends TestCase
{

    /**
     * The utility we want to test.
     *
     * @var \TechDivision\Import\Utils\ColumnPlaceholdersUtiInterface
     */
    protected $columnPlaceholdersUtil;

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
        $this->columnPlaceholdersUtil = new ColumnPlaceholdersUtil($mockColumnNameLoader, $mockTablePrefixUtil);
    }

    /**
     * Test's if the compile() method returns the properly compiled SQL.
     *
     * @return void
     */
    public function testCompile()
    {

        // create the statement that has to be compiled
        $statement = 'INSERT
               INTO catalog_product_entity_varchar
                    (store_id,attribute_id,value)
             VALUES (${column-placeholders:catalog_product_entity_varchar})';

        // create the SQL for the prepared statment we expect after compiling
        $expectedStatement = 'INSERT
               INTO catalog_product_entity_varchar
                    (store_id,attribute_id,value)
             VALUES (:store_id,:attribute_id,:value)';

        // assert that the statement has been compiled to a proper SQL
        $this->assertSame($expectedStatement, $this->columnPlaceholdersUtil->compile($statement));
    }
}
