<?php

/**
 * TechDivision\Import\Subjects\FileResolver\OkFileAwareFileResolverTest
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
* @copyright 2018 TechDivision GmbH <info@techdivision.com>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Subjects\FileResolver;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;

/**
 * Test class for the .OK file aware file resolver implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileAwareFileResolverTest extends TestCase
{

    /**
     * The .OK file aware file resolver instance we want to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $okFileAwareFileResolver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock application
        $mockApplication = $this->getMockBuilder(ApplicationInterface::class)->getMock();

        // create a mock registry processor
        $mockRegistryProcessor = $this->getMockBuilder(RegistryProcessorInterface::class)->getMock();

        // initialize the simple file resolver instance
        $this->okFileAwareFileResolver = $this->getMockBuilder(OkFileAwareFileResolverInstance::class)
                                              ->setMethods(array('getSourceDir'))
                                              ->setConstructorArgs(array($mockApplication, $mockRegistryProcessor))
                                              ->getMock();

        // mock the source directory which will be set by invoking the loadFiles() method
        $this->okFileAwareFileResolver->expects($this->any())
                                      ->method('getSourceDir')
                                      ->willReturn(__DIR__ . DIRECTORY_SEPARATOR . '_files');
    }

    /**
     * Test's if the only the files belonging to a bunch and an apropriate OK file exists, will be imported.
     *
     * @return void
     */
    public function testShouldBeHandledWithOkFileNeededAndFourBunchesAndTwoOkFiles()
    {

        // initialize the prefix and the actual date
        $suffix = 'csv';
        $prefix = 'magento-import';
        $directory = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        // prepare some files which are NOT part of a bunch
        $data = array(
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-171_01.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-171_02.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-171_03.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-172_01.%s', $prefix, $suffix), true),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-172_02.%s', $prefix, $suffix), true),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-172_03.%s', $prefix, $suffix), true),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-173_01.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-173_02.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-173_03.%s', $prefix, $suffix), false),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-174_01.%s', $prefix, $suffix), true),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-174_02.%s', $prefix, $suffix), true),
            array($directory . DIRECTORY_SEPARATOR . sprintf('%s_20190614-174_03.%s', $prefix, $suffix), true)
        );

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

        // mock a subject configuration instance
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the subject configuration methods
        $mockSubjectConfiguration->expects($this->any())
            ->method('getFileResolver')
            ->willReturn($mockFileResolverConfiguration);
        $mockSubjectConfiguration->expects($this->any())
            ->method('isOkFileNeeded')
            ->willReturn(true);

        // initialize the file resolver
        $this->okFileAwareFileResolver->setSubjectConfiguration($mockSubjectConfiguration);

        // make sure, that the file IS part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $this->okFileAwareFileResolver->shouldBeHandled($filename));
        }
    }
}
