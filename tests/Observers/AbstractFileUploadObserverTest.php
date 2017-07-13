<?php

/**
 * TechDivision\Import\Observers\AbstractFileUploadObserverTest
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

namespace TechDivision\Import\Observers;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractFileUploadObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract file upload observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AbstractFileUploadObserver
     */
    protected $abstractFileUploadObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // the abstract mock file upload observer
        $this->abstractFileUploadObserver = $this->getMockBuilder('TechDivision\Import\Observers\AbstractFileUploadObserver')
                                                 ->getMockForAbstractClass();
    }

    /**
     * Test the handle() method.
     */
    public function testHandle()
    {

        // create a dummy CSV file row
        $row = array(
            0 => $sourceImage = 'image/path/old/test.jpg',
            1 => null
        );

        // prepare the expected row structure after the observer
        $expectedRow = array(
            0 => $sourceImage,
            1 => $targetImage = 'image/path/new/test.jpg'
        );

        // prepare source/target column name
        $this->abstractFileUploadObserver
             ->expects($this->exactly(2))
             ->method('getSourceColumn')
             ->willReturn($sourceColumn = 'image_path');
        $this->abstractFileUploadObserver
             ->expects($this->once())
             ->method('getTargetColumn')
             ->willReturn($targetColumn = 'image_new_path');

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(sprintf('Successfully copied image %s => %s', $sourceImage, $targetImage))
                         ->willReturn(null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\FileUploadSubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\FileUploadSubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->exactly(2))
                    ->method('hasHeader')
                    ->withConsecutive(
                        array($sourceColumn),
                        array($sourceColumn)
                    )
                    ->willReturnOnConsecutiveCalls(true, true, true);
        $mockSubject->expects($this->exactly(3))
                    ->method('getHeader')
                    ->withConsecutive(
                        array($sourceColumn),
                        array($sourceColumn),
                        array($targetColumn)
                    )
                    ->willReturnOnConsecutiveCalls(0, 0, 1);
        $mockSubject->expects($this->once())
                    ->method('uploadFile')
                    ->with($sourceImage)
                    ->willReturn($targetImage);
        $mockSubject->expects($this->once())
                    ->method('hasCopyImages')
                    ->willReturn(true);
        $mockSubject->expects($this->once())
                    ->method('getParentImage')
                    ->willReturn('image/path/old/test-other.jpg');

        // assert that the returnd row has the expected structure
        $this->assertSame($expectedRow, $this->abstractFileUploadObserver->handle($mockSubject));
    }

    /**
     * Test the handle() method.
     */
    public function testHandleWithIsParentImageTrue()
    {

        // create a dummy CSV file row
        $row = array(
            0 => $sourceImage = 'image/path/old/test.jpg'
        );

        // prepare source/target column name
        $this->abstractFileUploadObserver
             ->expects($this->once())
             ->method('getSourceColumn')
             ->willReturn($sourceColumn = 'image_path');

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\FileUploadSubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\FileUploadSubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getParentImage')
                    ->willReturn($sourceImage);
        $mockSubject->expects($this->once())
                    ->method('hasHeader')
                    ->with($sourceColumn)
                    ->willReturn(true);
        $mockSubject->expects($this->once())
                     ->method('getHeader')
                     ->with($sourceColumn)
                     ->willReturn(0);

        // assert that the returnd row has the expected structure
        $this->assertSame($row, $this->abstractFileUploadObserver->handle($mockSubject));
    }
}
