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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\content\StringBasedFileContent;

/**
 * Test class for the filesystem trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FilesystemTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The filesystem trait that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\FilesystemTrait
     */
    protected $filesystemTrait;

    /**
     * The virtual filesystem root.
     *
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // initialize the trait we want to test
        $this->filesystemTrait = new FilesystemTraitImpl();

        // setup the filesystem
        $this->root = vfsStream::setup('/var/www/html');
    }

    /**
     * Test the set/getRootDir() methods.
     *
     * @return void
     */
    public function testSetGetRootDir()
    {
        $this->filesystemTrait->setRootDir($rootDir = '/var/www/html');
        $this->assertSame($rootDir, $this->filesystemTrait->getRootDir());
    }

    /**
     * Test the isFile() method.
     *
     * @return void
     */
    public function testIsFile()
    {

        // create a new file
        $file = vfsStream::newFile('my-test.image.txt')->withContent(new StringBasedFileContent('test'))->at($this->root);

        // query whether or not the file exists
        $this->assertTrue($this->filesystemTrait->isFile($file->url()));
    }

    /**
     * Test the isDir() method.
     *
     * @return void
     */
    public function testIsDir()
    {

        // create a new directory
        $dir = vfsStream::newDirectory('test')->at($this->root);

        // query whether or not the directory exists
        $this->assertTrue($this->filesystemTrait->isDir($dir->url()));
    }

    /**
     * Test the touch() method.
     *
     * @return void
     */
    public function testTouch()
    {

        // prepare the filename that has to be created
        $filename = 'vfs://var/www/html/test.txt';

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
        $oldname = vfsStream::newFile('my-test.image.txt')->withContent(new StringBasedFileContent('test'))->at($this->root)->url();
        $newname = 'vfs://var/www/html/test-new.txt';

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
        $dirname = 'vfs://var/www/html/test';

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
        $filename = 'vfs://var/www/html/test.txt';

        // assert that the file has been created
        $this->assertFalse($this->filesystemTrait->isFile($filename));
        $this->assertSame(14, $this->filesystemTrait->write($filename, $data = 'test test test'));
        $this->assertTrue($this->filesystemTrait->isFile($filename));
        $this->assertSame($data, file_get_contents($filename));
    }

    /**
     * Test the resolvePath() method with an absolute path.
     *
     * @return void
     */
    public function testResolvePathWithAbsolutePath()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->once())
                       ->method('has')
                       ->with($path = '/var/www/html/test.txt')
                       ->willReturn(true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystem($mockFilesystem);

        // query whether or not the path will be resolved
        $this->assertSame($path, $this->filesystemTrait->resolvePath($path));
    }

    /**
     * Test the resolvePath() method with a relative path.
     *
     * @return void
     */
    public function testResolvePathWithRelativePath()
    {

        // mock the filesystem
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                       ->method('has')
                       ->withConsecutive(
                           array($filename = 'test.txt'),
                           array($path = getcwd() . DIRECTORY_SEPARATOR . $filename)
                       )
                       ->willReturnOnConsecutiveCalls(false, true);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystem($mockFilesystem);

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
        $mockFilesystem = $this->getMockBuilder('League\Flysystem\FilesystemInterface')
                               ->setMethods(get_class_methods('League\Flysystem\FilesystemInterface'))
                               ->getMock();
        $mockFilesystem->expects($this->exactly(2))
                       ->method('has')
                       ->withConsecutive(
                               array($filename = 'test.txt'),
                               array($path = getcwd() . DIRECTORY_SEPARATOR . $filename)
                           )
                           ->willReturnOnConsecutiveCalls(false, false);

        // set the mock filesystem
        $this->filesystemTrait->setFilesystem($mockFilesystem);

        // query whether or not the path will be resolved
        $this->filesystemTrait->resolvePath($filename);
    }
}
