<?php

/**
 * TechDivision\Import\Plugins\SimpleFileResolverTest
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Subjects\FileResolver\SimpleFileResolver;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface;
use TechDivision\Import\Utils\BunchKeys;

/**
 * Test class for the simple file resolver implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleFileResolverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The file resolver instance we want to test.
     *
     * @var \TechDivision\Import\Subjects\FileResolver\SimpleFileResolver
     */
    protected $simpleFileResolver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock application
        $mockApplication = $this->getMockBuilder(ApplicationInterface::class)->getMock();

        // create a mock registry processor
        $mockRegistryProcessor = $this->getMockBuilder(RegistryProcessorInterface::class)->getMock();

        // initialize the simple file resolver instance
        $this->simpleFileResolver = new SimpleFileResolver($mockApplication, $mockRegistryProcessor);
    }

    /**
     * Test's if the passed file is NOT part of a bunch.
     *
     * @return void
     */
    public function testShouldBeHandledWithNoBunch()
    {

        // initialize the prefix and the actual date
        $suffix = 'csv';
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.%s', $prefix, $actualDate, $suffix), true),
            array(sprintf('import/add-update/%s_%s-173_02.%s', $prefix, $actualDate, $suffix), false),
            array(sprintf('import/add-update/%s_%s-174_03.%s', $prefix, $actualDate, $suffix), false),
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

        // mock a subject configuration instance
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the subject configuration methods
        $mockSubjectConfiguration->expects($this->any())
            ->method('getFileResolver')
            ->willReturn($mockFileResolverConfiguration);

        // initialize the file resolver
        $this->simpleFileResolver->setSubjectConfiguration($mockSubjectConfiguration);

        // make sure, that only the FIRST file is part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $this->simpleFileResolver->shouldBeHandled($filename));
        }
    }

    /**
     * Test's if the passed file IS part of a bunch.
     *
     * @return void
     */
    public function testShouldBeHandledWithBunch()
    {

        // initialize the prefix and the actual date
        $suffix = 'csv';
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.%s', $prefix, $actualDate, $suffix), true),
            array(sprintf('import/add-update/%s_%s-172_02.%s', $prefix, $actualDate, $suffix), true),
            array(sprintf('import/add-update/%s_%s-172_03.%s', $prefix, $actualDate, $suffix), true),
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

        // mock a subject configuration instance
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the subject configuration methods
        $mockSubjectConfiguration->expects($this->any())
            ->method('getFileResolver')
            ->willReturn($mockFileResolverConfiguration);

        // initialize the file resolver
        $this->simpleFileResolver->setSubjectConfiguration($mockSubjectConfiguration);

        // make sure, that the file IS part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $this->simpleFileResolver->shouldBeHandled($filename));
        }
    }
}
