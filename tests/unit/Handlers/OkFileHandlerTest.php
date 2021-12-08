<?php

/**
 * TechDivision\Import\Handlers\OkFileHandlerTest
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Adapter\PhpFilesystemAdapter;
use TechDivision\Import\Loaders\FilteredLoaderInterface;
/**
 * Test class for the symfony application implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileHandlerTest extends TestCase
{

    /**
     * The file writer instance that has to be tested.
     *
     * @var \TechDivision\Import\Handlers\OkFileHandler
     */
    protected $okFileHandler;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // creqte the mock for the generic file handler
        $mockGenericFileHandler = $this->getMockBuilder(GenericFileHandler::class)->getMock();

        // initialize the .OK file handler instance we want to test
        $this->okFileHandler = new OkFileHandler($mockGenericFileHandler);
    }

    /**
     * Initializes the .OK file hanlder instance we want to test.
     *
     * @param array  $files   The array with the .OK > .CSF files data
     * @param string $pattern The pattern passed to the mock loader
     *
     * @return \TechDivision\Import\Handlers\OkFileHandler
     */
    protected function getOkFileHandler(array $files = array(), string $pattern = '/.*/')
    {

        // initialize the mock loader instance
        $mockLoader = $this->getMockBuilder(FilteredLoaderInterface::class)->getMock();
        $mockLoader->expects($this->once())->method('load')->with($pattern)->willReturn($files);

        // inject the file resolver instance
        $this->okFileHandler->setLoader($mockLoader);

        // initialize the modk filesystem adapter
        $mockFilesystemAdapter = $this->getMockBuilder(PhpFilesystemAdapter::class)->getMock();

        // initialize the array withe mock invocations for the filesystem adapter's create() method
        $expected = array();
        array_walk($files, function(array $v, $k) use ($expected) {
            $expected = array($k, implode(PHP_EOL, $v));
        });

        // mock the filesystem adapter's write method
        $mockFilesystemAdapter
            ->expects($this->exactly(sizeof($files)))
            ->method('write')
            ->withConsecutive($expected);

        // inject the filesystem adapter instance
        $this->okFileHandler->setFilesystemAdapter($mockFilesystemAdapter);

        // return the initialized .OK file handler instance
        return $this->okFileHandler;
    }

    /**
     * Test the createOkFiles() method with two files.
     *
     * @return void
     */
    public function testCreateOkFilesWithTwoFiles()
    {

        // initialize the pattern to use
        $pattern = __DIR__ . DIRECTORY_SEPARATOR . 'magento-import.*.csv';

        // load and initialize the .OK file handler we want to test
        $okFileHandler = $this->getOkFileHandler(
            array(
                sprintf('%s/magento-import_20200326-145451.ok' , __DIR__) => array(
                    __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv'
                ),
                sprintf('%s/magento-import_20200327-145451.ok' , __DIR__) => array(
                    __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200327-145451_01.csv'
                )
            ),
            $pattern
        );

        // count the number of created .OK files
        $this->assertSame(2, $okFileHandler->createOkFiles($pattern));
    }

    /**
     * Test the createOkFiles() method with a bunch of tow files.
     *
     * @return void
     */
    public function testCreateOkFilesWithBunch()
    {

        // initialize the pattern to use
        $pattern = __DIR__ . DIRECTORY_SEPARATOR . 'magento-import.*.csv';

        // load and initialize the .OK file handler we want to test
        $okFileHandler = $this->getOkFileHandler(
            array(
                sprintf('%s/magento-import_20200326-145451.ok' , __DIR__) => array(
                    __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
                    __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_02.csv'
                )
            ),
            $pattern
        );

        // count the number of created .OK files
        $this->assertSame(1, $okFileHandler->createOkFiles($pattern));
    }
}
