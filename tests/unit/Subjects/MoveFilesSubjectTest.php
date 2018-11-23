<?php

/**
 * TechDivision\Import\Subjects\MoveFilesSubjectTest
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

/**
 * Test class for the subject that moves the import files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MoveFilesSubjectTest extends AbstractTest
{

    /**
     * The abstract subject that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\AbstractSubject
     */
    protected $moveFilesSubject;

    /**
     * The serial used to setup the subject.
     *
     * @var string
     */
    protected $serial;

    /**
     * The class name of the subject we want to test.
     *
     * @return string The class name of the subject
     */
    protected function getSubjectClassName()
    {
        return 'TechDivision\Import\Subjects\MoveFilesSubject';
    }

    /**
     * Return the subject's methods we want to mock.
     *
     * @return array The methods
     */
    protected function getSubjectMethodsToMock()
    {
        return array('match', 'rename', 'mkdir', 'isDir');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        // create the subject instance we want to test and invoke the setup method
        $this->moveFilesSubject= $this->getSubjectInstance(array('getDefaultCallbackMappings'));
        $this->moveFilesSubject->setUp($this->serial = uniqid());
    }

    /**
     * The the import() method with a not matching file.
     *
     * @return void
     */
    public function testImportWithNotMatchingFile()
    {

        // invoke the tear down and make sure no value will be returned
        $this->assertNull($this->moveFilesSubject->import($this->serial, 'test-test.csv'));
    }

    /**
     * The the import() method with a matching file and an existing source directory.
     *
     * @return void
     */
    public function testImportWithMatchingFileAndExistingSourceDir()
    {

        // initialize the filename
        $filename = sprintf('%s/product-import_20170711-110012_01.csv', $targetDir = 'var/importexport');

        // mock the method returning the new source directory
        $this->moveFilesSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getTargetDir')
             ->willReturn($targetDir = 'var/importexport');

        // mock the isDir() method
        $this->moveFilesSubject
             ->expects($this->once())
             ->method('isDir')
             ->with($newSourceDir = sprintf('%s/%s', $targetDir, $this->serial))
             ->willReturn(true);

        // mock the rename() method
        $this->moveFilesSubject
             ->expects($this->once())
             ->method('rename')
             ->with($filename, sprintf('%s/%s', $newSourceDir, basename($filename)))
             ->willReturn(true);

        // set the serial
        $this->moveFilesSubject->setSerial($this->serial);

        // invoke the import method
        $this->assertNull($this->moveFilesSubject->import($this->serial, $filename));
    }

    /**
     * The the import() method with a matching file and a not existing source directory.
     *
     * @return void
     */
    public function testImportWithMatchingFileAndNotExistingSourceDir()
    {

        // initialize the filename
        $filename = sprintf('%s/product-import_20170711-110012_01.csv', $targetDir = 'var/importexport');

        // mock the getter for the new source directory
        $this->moveFilesSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getTargetDir')
             ->willReturn($targetDir= 'var/importexport');

        // mock the isDir() method
        $this->moveFilesSubject
             ->expects($this->once())
             ->method('isDir')
             ->with($newSourceDir = sprintf('%s/%s', $targetDir, $this->serial))
             ->willReturn(false);

        // mock the mkdir() method
        $this->moveFilesSubject
             ->expects($this->once())
             ->method('mkdir')
             ->with($newSourceDir)
             ->willReturn(true);

        // mock the rename() method
        $this->moveFilesSubject
             ->expects($this->once())
             ->method('rename')
             ->with($filename, sprintf('%s/%s', $newSourceDir, basename($filename)))
             ->willReturn(true);

        // set the serial
        $this->moveFilesSubject->setSerial($this->serial);

        // invoke the import method
        $this->assertNull($this->moveFilesSubject->import($this->serial, $filename));
    }

    /**
     * Test the getHeaderMappings() method.
     *
     * @return void
     */
    public function testGetHeaderMappings()
    {
        $this->assertCount(0, $this->moveFilesSubject->getHeaderMappings());
    }

    /**
     * Test the getDefaultFrontendInputCallbackMappings() method.
     *
     * @return void
     */
    public function testGetDefaultFrontendInputCallbackMappings()
    {
        $this->assertCount(0, $this->moveFilesSubject->getDefaultFrontendInputCallbackMappings());
    }
}
