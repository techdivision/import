<?php

/**
 * TechDivision\Import\Subjects\ExportableTraitTest
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\ColumnKeys;

/**
 * Test class for the exportable trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ExportableTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The exportable trait that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    protected $exportableTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->exportableTrait = $this->getMockForAbstractClass('TechDivision\Import\Subjects\ExportableTraitImpl');
    }

    /**
     * Test the set/hasExportAdapter() methods.
     *
     * @return void
     */
    public function testSetGetExportAdapter()
    {

        // mock the export adapter
        $mockExportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ExportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ExportAdapterInterface'))
                                  ->getMock();

        // set the mock export adapter and check the return type
        $this->exportableTrait->setExportAdapter($mockExportAdapter);
        $this->assertInstanceOf(
            'TechDivision\Import\Adapter\ExportAdapterInterface',
            $this->exportableTrait->getExportAdapter()
        );
    }

    /**
     * Test the addArtefacts() method with an empty array of artefacts.
     *
     * @return void
     */
    public function testAddArtefactsWithEmptyArray()
    {
        $this->assertNull($this->exportableTrait->addArtefacts('product-variants', array()));
        $this->assertCount(0, $this->exportableTrait->getArtefacts());
    }

    /**
     * Test the addArtefacts() method with orginial data has been serialized.
     *
     * @return void
     */
    public function testAddArtefactsWithWithOriginalData()
    {

        // set last entity ID
        $this->exportableTrait->setLastEntityId($lastEntityId = 1000);

        // initialize the artefact type
        $type = 'product-variants';

        // initialize the artefacts to import
        $artefacts = array(
            array(
                ColumnKeys::ORIGINAL_DATA  => array(
                    ColumnKeys::ORIGINAL_FILENAME     => 'product-import_20170101123000_01.csv',
                    ColumnKeys::ORIGINAL_LINE_NUMBER  => 2,
                    ColumnKeys::ORIGINAL_COLUMN_NAMES => array('col1', 'col2')
                ),
                ColumnKeys::ATTRIBUTE_CODE => 'a_attribute_code',
                ColumnKeys::VALUE          => 1001
            )
        );

        // prepare the expected result
        $expectedArtefacts = array();
        foreach ($artefacts as $key => $artefact) {
            $expectedArtefacts[$key] = $artefact;
            $expectedArtefacts[$key][ColumnKeys::ORIGINAL_DATA] = serialize(
                $expectedArtefacts[$key][ColumnKeys::ORIGINAL_DATA]
            );
        }

        // initialize the artefacts to import
        $artfactsToCompare = array(
            $type => array($lastEntityId => array($expectedArtefacts))
        );

        // assert that the original data has been serialized
        $this->assertNull($this->exportableTrait->addArtefacts($type, $artefacts));
        $this->assertCount(1, $this->exportableTrait->getArtefacts());
        $this->assertSame($artfactsToCompare, $this->exportableTrait->getArtefacts());
    }

    /**
     * Test the export() method.
     *
     * @return void
     */
    public function testExport()
    {

        // set the last entity ID
        $this->exportableTrait->setLastEntityId($lastEntityId = 100);

        // mock the trait methods
        $this->exportableTrait->expects($this->once())
                              ->method('getTargetDir')
                              ->willReturn($targetDir = 'var/importexport');

        // initialize the artefact type
        $type = 'product-variants';

        // initialize the artefacts to import
        $artfacts = array(
            array(
                ColumnKeys::ATTRIBUTE_CODE => 'a_attribute_code',
                ColumnKeys::VALUE          => 1001
            )
        );

        // initialize the artefacts to import
        $artfactsToCompare = array(
            $type => array($lastEntityId => array($artfacts))
        );

        // mock the export adapter
        $mockExportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ExportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ExportAdapterInterface'))
                                  ->getMock();
        $mockExportAdapter->expects($this->once())
                          ->method('export')
                          ->with(
                              $artfactsToCompare,
                              $targetDir,
                              $timestamp = time(),
                              $counter = 1
                          )
                          ->willReturn(null);

        // set the mock export adapter
        $this->exportableTrait->setExportAdapter($mockExportAdapter);

        // add the artefacts
        $this->exportableTrait->addArtefacts($type, $artfacts);

        // export the artefacts
        $this->exportableTrait->export($timestamp, $counter);
    }

    /**
     * Test the getArtefactsByTypeAndEntityId() method.
     *
     * @return void
     */
    public function testGetArtefactsByTypeAndEntityId()
    {

        // set serial, filename, linenumber and last entity ID
        $this->exportableTrait->setLastEntityId($lastEntityId = 1000);

        // initialize the artefact type
        $type = 'product-variants';

        // initialize the artefacts to import
        $artfacts = array(
            array(
                ColumnKeys::ATTRIBUTE_CODE => 'a_attribute_code',
                ColumnKeys::VALUE          => 1001
            )
        );

        // add the artefacts and compare the result
        $this->exportableTrait->addArtefacts($type, $artfacts);
        $this->assertCount(1, $this->exportableTrait->getArtefactsByTypeAndEntityId($type, $lastEntityId));
        $this->assertSame($artfacts, $this->exportableTrait->getArtefactsByTypeAndEntityId($type, $lastEntityId));
    }

    /**
     * Test the newArtefact() method.
     *
     * @return void
     */
    public function testNewArtefact()
    {

        // mock the trait methods
        $this->exportableTrait->expects($this->once())
                              ->method('getFilename')
                              ->willReturn($filename = 'product-import_20170101123000_01.csv');
        $this->exportableTrait->expects($this->once())
                              ->method('getLineNumber')
                              ->willReturn($lineNumber = 2);

        // prepare the original data
        $originalData = array(
            ColumnKeys::ORIGINAL_FILENAME     => $filename,
            ColumnKeys::ORIGINAL_LINE_NUMBER  => $lineNumber,
            ColumnKeys::ORIGINAL_COLUMN_NAMES => $originalColumnNames = array('store_view_code' => 'svc')
        );

        // prepare the columns
        $columns = array('store_view_code' => 'en_US');

        // prepare the expected result
        $expectedResult = array_merge(array(ColumnKeys::ORIGINAL_DATA => $originalData), $columns);

        // assert that the new artefact has the expected structure
        $this->assertSame($expectedResult, $this->exportableTrait->newArtefact($columns, $originalColumnNames));
    }

    /**
     * Test the getArtefactsByTypeAndEntityId() method with invalid type or entity ID.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Cant't load artefacts for type unknown_type and entity ID 10
     */
    public function testGetArtefactsByTypeAndEntityIdWithException()
    {
        $this->exportableTrait->getArtefactsByTypeAndEntityId('unknown_type', 10);
    }
}
