<?php

/**
 * TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriterTest
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

namespace TechDivision\Import\Subjects\FileWriter;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Adapter\PhpFilesystemAdapter;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Subjects\FileResolver\SimpleFileResolver;
use TechDivision\Import\Subjects\FileWriter\Filters\DefaultOkFileFilter;
use TechDivision\Import\Subjects\FileWriter\Sorters\DefaultOkFileSorter;
use TechDivision\Import\Utils\BunchKeys;

/**
 * Test class for the symfony application implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileAwareFileWriterTest extends TestCase
{

    /**
     * The file writer instance that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
     */
    protected $okFileAwareFileWriter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {
        $this->okFileAwareFileWriter = new OkFileAwareFileWriter();
    }

    /**
     *
     * @param array $files
     * @param string $suffix
     * @param string $prefix
     * @return \TechDivision\Import\Subjects\FileWriter\OkFileAwareFileWriter
     */
    protected function getOkFileAwareFileWriter(array $files = array(), $suffix = 'csv', $prefix = 'magento-import')
    {

        // create a mock application
        $mockApplication = $this->getMockBuilder(ApplicationInterface::class)->getMock();

        // create a mock registry processor
        $mockRegistryProcessor = $this->getMockBuilder(RegistryProcessorInterface::class)->getMock();


        $mockFilesystemAdapter = $this->getMockBuilder(PhpFilesystemAdapter::class)->setMethods(array('write'))->getMock();

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
            ->willReturn($suffix);
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getPrefix')
            ->willReturn($prefix);
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getFilename')
            ->willReturn('.*');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getCounter')
            ->willReturn('\d+');
        $mockFileResolverConfiguration->expects($this->any())
            ->method('getOkFileSuffix')
            ->willReturn('ok');

        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        $mockSubjectConfiguration
            ->expects($this->any())
            ->method('getFileResolver')
            ->willReturn($mockFileResolverConfiguration);

        $simpleFileResolver = $this->getMockBuilder(SimpleFileResolver::class)
            ->setConstructorArgs(array($mockApplication, $mockRegistryProcessor))
            ->setMethods(array('getSubjectConfiguration', 'loadFiles'))
            ->getMock();

        $simpleFileResolver->setFilesystemAdapter($mockFilesystemAdapter);

        $simpleFileResolver
            ->expects($this->any())
            ->method('getSubjectConfiguration')
            ->willReturn($mockSubjectConfiguration);
        $simpleFileResolver
            ->expects($this->any())
            ->method('loadFiles')
            ->willReturn($files);

        // inject the file resolver instacne
        $this->okFileAwareFileWriter->setFileResolver($simpleFileResolver);

        $this->okFileAwareFileWriter->addFilter(new DefaultOkFileFilter());
        $this->okFileAwareFileWriter->addSorter(new DefaultOkFileSorter());

        return $this->okFileAwareFileWriter;
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testProposedOkFilenames()
    {

        $okFileAwareFileWriter = $this->getOkFileAwareFileWriter(array(__DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv'));

        $expectedOkFilenames = array(__DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451.ok');

        $this->assertSame($expectedOkFilenames, array_keys($okFileAwareFileWriter->propsedOkFilenames(uniqid())));
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testProposedOkFilenamesWithBunch()
    {

        $okFileAwareFileWriter = $this->getOkFileAwareFileWriter(array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_02.csv'
        ));

        $expectedOkFilenames = array(__DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451.ok');

        $this->assertSame($expectedOkFilenames, array_keys($okFileAwareFileWriter->propsedOkFilenames(uniqid())));
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testProposedOkFilenamesWithMultipleFiles()
    {


        $okFileAwareFileWriter = $this->getOkFileAwareFileWriter(array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200327-145451_01.csv'
        ));

        $expectedOkFilenames = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451.ok',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200327-145451.ok'
        );

        $this->assertSame($expectedOkFilenames, array_keys($okFileAwareFileWriter->propsedOkFilenames(uniqid())));
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testCreateTwoOkFiles()
    {

        $okFileAwareFileWriter = $this->getOkFileAwareFileWriter(array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200327-145451_01.csv'
        ));

        $fileResolver = $okFileAwareFileWriter->getFileResolver();

        $fileResolver->getFilesystemAdapter()
            ->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                array(sprintf('%s/magento-import_20200326-145451.ok' , __DIR__), 'magento-import_20200326-145451_01.csv'),
                array(sprintf('%s/magento-import_20200327-145451.ok' , __DIR__), 'magento-import_20200327-145451_01.csv')
            )
            ->willReturnOnConsecutiveCalls(
                strlen('magento-import_20200326-145451_01.csv'),
                strlen('magento-import_20200327-145451_01.csv')
            );

        $this->assertSame(2, $okFileAwareFileWriter->createOkFiles(uniqid()));
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testCreateOneOkFilenameWithBunch()
    {

        $okFileAwareFileWriter = $this->getOkFileAwareFileWriter(array(
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_01.csv',
            __DIR__ . DIRECTORY_SEPARATOR . 'magento-import_20200326-145451_02.csv'
        ));

        $expectedCsvFilenames = array(
            'magento-import_20200326-145451_01.csv',
            'magento-import_20200326-145451_02.csv'
        );

        $fileResolver = $okFileAwareFileWriter->getFileResolver();

        $fileResolver->getFilesystemAdapter()
            ->expects($this->any())
            ->method('write')
            ->with(sprintf('%s/magento-import_20200326-145451.ok' , __DIR__), implode(PHP_EOL, $expectedCsvFilenames))
            ->willReturn(strlen(implode($expectedCsvFilenames, PHP_EOL)));

        $this->assertSame(1, $okFileAwareFileWriter->createOkFiles(uniqid()));
    }
}
