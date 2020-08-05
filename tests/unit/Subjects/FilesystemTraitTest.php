<?php

/**
 * TechDivision\Import\Subjects\FilesystemTraitTest
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

use PHPUnit\Framework\TestCase;

/**
 * Test class for the filesystem trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FilesystemTraitTest extends TestCase
{

    /**
     * The filesystem trait that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\FilesystemTrait
     */
    protected $filesystemTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {
        $this->filesystemTrait = new FilesystemTraitImpl();
    }

    /**
     * Test the isFile() method.
     *
     * @return void
     */
    public function testIsFile()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('isFile')
                       ->with($url = '/var/www/my-test.image.txt')
                       ->willReturn(true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // query whether or not the file exists
        $this->assertTrue($this->filesystemTrait->isFile($url));
    }

    /**
     * Test the isDir() method.
     *
     * @return void
     */
    public function testIsDir()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('isDir')
                       ->with($url = '/var/www/test')
                       ->willReturn(true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // query whether or not the directory exists
        $this->assertTrue($this->filesystemTrait->isDir($url));
    }

    /**
     * Test the touch() method.
     *
     * @return void
     */
    public function testTouch()
    {

        // prepare the filename that has to be created
        $filename = '/var/www/html/test.txt';

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('touch')
                       ->with($filename)
                       ->willReturn(true);
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isFile')
                       ->withConsecutive(
                           array($filename),
                           array($filename)
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // assert that the file has been created
        $this->assertFalse($this->filesystemTrait->isFile($filename));
        $this->assertTrue($this->filesystemTrait->touch($filename));
        $this->assertTrue($this->filesystemTrait->isFile($filename));
    }

    /**
     * Test the rename() method.
     *
     * @return void
     */
    public function testRename()
    {

        // prepare the old and the new filename
        $oldname = '/var/www/my-test.image.txt';
        $newname = '/var/www/html/test-new.txt';

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('rename')
                       ->with($oldname, $newname)
                       ->willReturn(true);
        $mockFilesystem->expects($this->exactly(4))
                       ->method('isFile')
                       ->withConsecutive(
                           array($oldname),
                           array($newname),
                           array($oldname),
                           array($newname)
                       )
                       ->willReturnOnConsecutiveCalls(true, false, false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // assert that the file has been renamed
        $this->assertTrue($this->filesystemTrait->isFile($oldname));
        $this->assertFalse($this->filesystemTrait->isFile($newname));
        $this->assertTrue($this->filesystemTrait->rename($oldname, $newname));
        $this->assertFalse($this->filesystemTrait->isFile($oldname));
        $this->assertTrue($this->filesystemTrait->isFile($newname));
    }

    /**
     * Test the mkdir() method.
     *
     * @return void
     */
    public function testMkdir()
    {

        // prepare the directory name that has to be created
        $dirname = '/var/www/html/test';

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('mkdir')
                       ->with($dirname)
                       ->willReturn(true);
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isDir')
                       ->withConsecutive(
                           array($dirname),
                           array($dirname)
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // assert that the directory has been created
        $this->assertFalse($this->filesystemTrait->isDir($dirname));
        $this->assertTrue($this->filesystemTrait->mkdir($dirname));
        $this->assertTrue($this->filesystemTrait->isDir($dirname));
    }

    /**
     * Test the write() method.
     *
     * @return void
     */
    public function testWrite()
    {

        // prepare the filename that has to be created
        $filename = '/var/www/html/test.txt';

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('write')
                       ->with($filename, $data = 'test test test')
                       ->willReturn(14);
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isFile')
                       ->withConsecutive(
                           array($filename),
                           array($filename)
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // assert that the file has been created
        $this->assertFalse($this->filesystemTrait->isFile($filename));
        $this->assertSame(14, $this->filesystemTrait->write($filename, $data));
        $this->assertTrue($this->filesystemTrait->isFile($filename));
    }

    /**
     * Test the resolvePath() method with an absolute path.
     *
     * @return void
     */
    public function testResolvePathWithAbsolutePath()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isDir')
                       ->withConsecutive(
                           array($path0 = '/var/www/html/test.txt'),
                           array($path1 = getcwd() . DIRECTORY_SEPARATOR . ltrim($path0, '/'))
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // query whether or not the path will be resolved
        $this->assertSame($path1, $this->filesystemTrait->resolvePath($path0));
    }

    /**
     * Test the resolvePath() method with a relative path.
     *
     * @return void
     */
    public function testResolvePathWithRelativePath()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isDir')
                       ->withConsecutive(
                           array($filename = 'test.txt'),
                           array($path = getcwd() . DIRECTORY_SEPARATOR . $filename)
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // query whether or not the path will be resolved
        $this->assertSame($path, $this->filesystemTrait->resolvePath($filename));
    }

    /**
     * Test the resolvePath() method with an invalid path.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Directory test.txt doesn't exist
     */
    public function testResolvePathWithInvalidPath()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('TechDivision\Import\Adapter\FilesystemAdapterInterface')
                               ->setMethods(get_class_methods('TechDivision\Import\Adapter\FilesystemAdapterInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                       ->method('isDir')
                       ->withConsecutive(
                               array($filename = 'test.txt'),
                               array(getcwd() . DIRECTORY_SEPARATOR . $filename)
                           )
                           ->willReturnOnConsecutiveCalls(false, false);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystemAdapter($mockFilesystem);

        // query whether or not the path will be resolved
        $this->filesystemTrait->resolvePath($filename);
    }
}
