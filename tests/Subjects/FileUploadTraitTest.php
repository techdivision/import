<?php

/**
 * TechDivision\Import\Subjects\AbstractEavSubjectTest
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
 * Test class for the file upload trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FileUploadTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The file upload trait that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\MockForFileUploadTrait
     */
    protected $fileUploadTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->fileUploadTrait = new MockForFileUploadTrait();
    }

    /**
     * Test the set/hasCopyImages() methods.
     *
     * @return void
     */
    public function testSetGetCopyImages()
    {
        $this->fileUploadTrait->setCopyImages($copyImages = true);
        $this->assertSame($copyImages, $this->fileUploadTrait->hasCopyImages());
    }

    /**
     * Test the set/getMediaDir() methods.
     *
     * @return void
     */
    public function testSetGetMediaDir()
    {
        $this->fileUploadTrait->setMediaDir($mediaDir = 'var/importexport/media');
        $this->assertSame($mediaDir, $this->fileUploadTrait->getMediaDir());
    }

    /**
     * Test the set/getImagesFileDir() methods.
     *
     * @return void
     */
    public function testSetGetImagesFileDir()
    {
        $this->fileUploadTrait->setImagesFileDir($imagesFileDir = 'pub/images');
        $this->assertSame($imagesFileDir, $this->fileUploadTrait->getImagesFileDir());
    }

    /**
     * Test the set/getParentImage() methods.
     *
     * @return void
     */
    public function testSetGetParentImage()
    {
        $this->fileUploadTrait->setParentImage($parentImage = 'var/importexport/pub/images/test-01.jpg');
        $this->assertSame($parentImage, $this->fileUploadTrait->getParentImage());
    }

    /**
     * Test the getNewFileName() method when the file not already exists.
     *
     * @return void
     */
    public function testGetNewFileNameWithNotExistingFile()
    {

        // initialize old and new filename
        $targetFilename = 'pub/media/images/test.jpg';
        $expectedFilename = basename($targetFilename);

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('has')
                       ->with($targetFilename)
                       ->willReturn(false);

        // set the mock filesystem
        $this->fileUploadTrait->setFilesystem($mockFilesystem);

        // query whether or not the new file name is the same as the passed one
        $this->assertSame($expectedFilename, $this->fileUploadTrait->getNewFileName($targetFilename));
    }

    /**
     * Test the getNewFileName() method when the file already exists.
     *
     * @return void
     */
    public function testGetNewFileNameWithExistingFile()
    {

        // initialize old and new filename
        $targetFilename = 'pub/media/images/test.jpg';
        $expectedFilename = 'test_1.jpg';

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(3))
                       ->method('has')
                       ->withConsecutive(
                           array($targetFilename),
                           array($targetFilename),
                           array('pub/media/images/test_1.jpg')
                       )
                       ->willReturnOnConsecutiveCalls(true, true, false);

        // set the mock filesystem
        $this->fileUploadTrait->setFilesystem($mockFilesystem);

        // query whether or not the new file name is the same as the passed one
        $this->assertSame($expectedFilename, $this->fileUploadTrait->getNewFileName($targetFilename));
    }

    /**
     * Test the uploadFile() method.
     *
     * @return void
     */
    public function testUploadFile()
    {

        // set media + images file directory
        $this->fileUploadTrait->setMediaDir($mediaDir = 'var/importexport/media');
        $this->fileUploadTrait->setImagesFileDir($imagesFileDir = 'pub/images');

        // prepare basename, filename and the name of the uploaded file
        $basename = '/a/b/test.jpg';
        $filename = sprintf('%s%s', $imagesFileDir, $basename);
        $uploadedFilename = sprintf('%s%s', $mediaDir, $basename);

        // mock the filesystem and its methods
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                        ->method('has')
                        ->withConsecutive(
                            array($filename),
                            array($uploadedFilename)
                        )
                        ->willReturnOnConsecutiveCalls(true, false);
        $mockFilesystem->expects($this->once())
                       ->method('copy')
                       ->with($filename, $uploadedFilename)
                       ->willReturn(null);

        // set the mock filesystem
        $this->fileUploadTrait->setFilesystem($mockFilesystem);

        // query whether or not the uploaded file has the expected name
        $this->assertSame($basename, $this->fileUploadTrait->uploadFile($basename));
    }

    /**
     * Test the uploadFile() method with a not existing file.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Media file pub/images/a/b/test.jpg not available
     */
    public function testUploadFileWithNotExistingFile()
    {

        // set media + images file directory
        $this->fileUploadTrait->setMediaDir($mediaDir = 'var/importexport/media');
        $this->fileUploadTrait->setImagesFileDir($imagesFileDir = 'pub/images');

        // prepare basename, filename and the name of the uploaded file
        $basename = '/a/b/test.jpg';
        $filename = sprintf('%s%s', $imagesFileDir, $basename);

        // mock the filesystem and its methods
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('has')
                       ->with($filename)
                       ->willReturn(false);

        // set the mock filesystem
        $this->fileUploadTrait->setFilesystem($mockFilesystem);

        // query whether or not the uploaded file has the expected name
        $this->fileUploadTrait->uploadFile($basename);
    }
}
