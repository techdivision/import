<?php

/**
 * TechDivision\Import\Loaders\Filters\OkFileFilterTest
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders\Filters;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Handlers\OkFileHandlerInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Test class for the symfony application implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileFilterTest extends TestCase
{

    /**
     * The file writer instance that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
     */
    protected $okFileFilter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock file handler
        $mockOkFileHandler = $this->getMockBuilder(OkFileHandlerInterface::class)->getMock();
        $mockOkFileHandler->expects($this->any())->method('cleanUpOkFile')->willReturn(true);
        $mockOkFileHandler->expects($this->any())->method('isOkFile')->willReturnCallback(function ($value) {
            return basename($value) === 'magento-import_20200326-145451.ok' ? true : false;
        });

        // mock a file resolver configuration instance
        $mockFileResolverConfiguration = $this->getMockBuilder(FileResolverConfigurationInterface::class)->getMock();

        // mock the file resolver configuration methods
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getElementSeparator')
            ->willReturn('_');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getPatternElements')
            ->willReturn(BunchKeys::getAllKeys());
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getSuffix')
            ->willReturn('csv');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getPrefix')
            ->willReturn('magento-import');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getFilename')
            ->willReturn('.*');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getCounter')
            ->willReturn('\d+');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getOkFileSuffix')
            ->willReturn('ok');

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the subject configuration methods
        $mockSubjectConfiguration
            ->expects($this->any())
            ->method('getFileResolver')
            ->willReturn($mockFileResolverConfiguration);
        $mockSubjectConfiguration->expects($this->any())
            ->method('isOkFileNeeded')
            ->willReturn(true);

        // create a new instance of the .OK file filter
        $this->okFileFilter = new OkFileFilter($mockOkFileHandler, $mockSubjectConfiguration, __DIR__);
    }

    /**
     * Test the filter's __invoke() method with a single file.
     *
     * @return void
     */
    public function testInvokeOnOneFile()
    {

        // initialize the array with the mock CSV files
        $csvFiles = array(__DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv');

        // assert that the CSV file has been filtered
        $this->assertSame($csvFiles,array_filter($csvFiles, $this->okFileFilter));
    }

    /**
     * Test the filter's __invoke() method with multiple files that are part of a bunch.
     *
     * @return void
     */
    public function testInvokeOnBunchOfFiles()
    {


        // initialize the array with the mock CSV files
        $csvFiles = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_02.csv'
        );

        // assert that the CSV file has been filtered
        $this->assertSame($csvFiles, array_filter($csvFiles, $this->okFileFilter));
    }

    /**
     * Test the filter's __invoke() method with multiple files that are not part of a bunch.
     *
     * @return void
     */
    public function testInvokeOnMultipleFiles()
    {

        // initialize the array with the mock CSV files
        $csvFiles = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200327-145451_01.csv'
        );

        // initialize the array with the filtered CSV files
        $filteredCsvFilenames = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv'
        );

        // assert that the CSV file has been filtered
        $this->assertSame($filteredCsvFilenames, array_filter($csvFiles, $this->okFileFilter));
    }
}
