<?php

/**
 * TechDivision\Import\Plugins\SubjectPluginTest
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\RegistryKeys;

/**
 * Test class for the subject plugin implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The mock appliction instance.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockApplication;

    /**
     * The mock subject factory instance.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSubjectExecutor;

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Plugins\SubjectPlugin
     */
    protected $subject;

    /**
     * Prepare the OK filename.
     *
     * @var string
     */
    protected $okFilename = __DIR__ . '/_files/product-import.ok';

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
        $this->mockApplication = $this->getMockBuilder('TechDivision\Import\ApplicationInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\ApplicationInterface'))
                                      ->getMock();

        // create a mock subject executor
        $this->mockSubjectExecutor = $this->getMockBuilder('TechDivision\Import\Plugins\SubjectExecutorInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Plugins\SubjectExecutorInterface'))
                                         ->getMock();

        // initialize the subject instance
        $this->subject = $this->getMockBuilder('TechDivision\Import\Plugins\SubjectPlugin')
                              ->setConstructorArgs(
                                  array(
                                      $this->mockApplication,
                                      $this->mockSubjectExecutor
                                  )
                              )
                              ->setMethods(array('lock', 'unlock', 'removeLineFromFile'))
                              ->getMock();

        // create the dummy .ok file
        file_put_contents($this->okFilename, 'product-import_20170720-125052_01.csv');
    }

    /**
     * Remove the OK filename.
     *
     * @return void
     */
    protected function tearDown()
    {
        unlink($this->okFilename);
    }

    /**
     * Tests's the plugin's process method.
     *
     * @return void
     */
    public function testProcessWithoutSubjects()
    {

        // mock tha basic data
        $bunches = 0;
        $status = array();
        $serial = uniqid();

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->exactly(2))
                              ->method('mergeAttributesRecursive')
                              ->withConsecutive(
                                  array($serial, $status),
                                  array($serial, array(RegistryKeys::BUNCHES => $bunches))
                              )
                              ->willReturn(null);

        // mock the configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\ConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->once())
                                  ->method('getOperationName')
                                  ->willReturn('add-update');
        $mockConfiguration->expects($this->once())
                                  ->method('getSourceDir')
                                  ->willReturn('var/importexport');

        // mock the application methods
        $this->mockApplication->expects($this->exactly(2))
                              ->method('getRegistryProcessor')
                              ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->exactly(2))
                              ->method('getSerial')
                              ->willReturn($serial);
        $this->mockApplication->expects($this->once())
                              ->method('stop')
                              ->willReturn(null);
        $this->mockApplication->expects($this->any())
                              ->method('getConfiguration')
                              ->willReturn($mockConfiguration);

        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();
        $mockPluginConfiguration->expects($this->once())
                                ->method('getSubjects')
                                ->willReturn(array());
        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // invoke the process() method
        $this->subject->process();
    }

    /**
     * Tests's the plugin's process method with a subject.
     *
     * @return void
     */
    public function testProcessWithOneSubject()
    {

        // prepare the source directory
        $sourceDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        // mock tha basic data
        $bunches = 1;
        $serial = uniqid();
        $status = array(RegistryKeys::SOURCE_DIRECTORY => $sourceDir);

        // mock the subject
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                         ->getMock();
        $mockSubjectConfiguration->expects($this->exactly(2))
                                 ->method('getPrefix')
                                 ->willReturn($prefix = 'product-import');
        $mockSubjectConfiguration->expects($this->exactly(3))
                                 ->method('getSuffix')
                                 ->willReturn('csv');
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getId')
                                 ->willReturn('a.subject.id');
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('isOkFileNeeded')
                                 ->willReturn(true);

        // mock the array with subjects
        $mockSubjectConfigurations = array($mockSubjectConfiguration);

        // mock the system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->exactly(2))
                              ->method('mergeAttributesRecursive')
                              ->withConsecutive(
                                  array($serial, array($prefix => array())),
                                  array($serial, array(RegistryKeys::BUNCHES => $bunches))
                              )
                              ->willReturn(null);
        $mockRegistryProcessor->expects($this->once())
                              ->method('getAttribute')
                              ->with($serial)
                              ->willReturn($status);

        // mock the configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\ConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->exactly(3))
                                  ->method('getSourceDir')
                                  ->willReturn($sourceDir);

        // mock the subject factory
        $this->mockSubjectExecutor->expects($this->once())
                                 ->method('execute')
                                 ->willReturn(null);

        // mock the application methods
        $this->mockApplication->expects($this->exactly(3))
                              ->method('getRegistryProcessor')
                              ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->exactly(4))
                              ->method('getSerial')
                              ->willReturn($serial);
        $this->mockApplication->expects($this->any())
                              ->method('getConfiguration')
                              ->willReturn($mockConfiguration);
        $this->mockApplication->expects($this->any())
                              ->method('getSystemLogger')
                              ->willReturn($mockSystemLogger);

        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();
        $mockPluginConfiguration->expects($this->once())
                                ->method('getSubjects')
                                ->willReturn($mockSubjectConfigurations);

        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // expect the unlock method
        $this->subject->expects($this->once())
                      ->method('unlock')
                      ->willReturn(null);
        $this->subject->expects($this->once())
                      ->method('removeLineFromFile')
                      ->willReturn(null);

        // invoke the process() method
        $this->subject->process();
    }

    /**
     * Tests's the plugin's process method with a subject.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Can't export file
     */
    public function testProcessWithOneSubjectAndException()
    {

        // prepare the source directory
        $sourceDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        // mock tha basic data
        $serial = uniqid();
        $status = array(RegistryKeys::SOURCE_DIRECTORY => $sourceDir);

        // mock the subject
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                         ->getMock();
        $mockSubjectConfiguration->expects($this->exactly(2))
                                 ->method('getPrefix')
                                 ->willReturn($prefix = 'product-import');
        $mockSubjectConfiguration->expects($this->exactly(3))
                                 ->method('getSuffix')
                                 ->willReturn('csv');
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('isOkFileNeeded')
                                 ->willReturn(true);

        // mock the array with subjects
        $mockSubjectConfigurations = array($mockSubjectConfiguration);

        // mock the system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->exactly(1))
                              ->method('mergeAttributesRecursive')
                              ->with($serial, array($prefix => array()))
                              ->willReturn(null);
        $mockRegistryProcessor->expects($this->once())
                              ->method('getAttribute')
                              ->with($serial)
                              ->willReturn($status);

        // mock the configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\ConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->exactly(3))
                                  ->method('getSourceDir')
                                  ->willReturn($sourceDir);

        // mock the subject factory
        $this->mockSubjectExecutor->expects($this->once())
                                  ->method('execute')
                                  ->willThrowException(new \Exception('Can\'t export file'));

        // mock the application methods
        $this->mockApplication->expects($this->exactly(2))
                              ->method('getRegistryProcessor')
                              ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->exactly(3))
                              ->method('getSerial')
                              ->willReturn($serial);
        $this->mockApplication->expects($this->any())
                              ->method('getConfiguration')
                              ->willReturn($mockConfiguration);
        $this->mockApplication->expects($this->any())
                              ->method('getSystemLogger')
                              ->willReturn($mockSystemLogger);

        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();
        $mockPluginConfiguration->expects($this->once())
                                ->method('getSubjects')
                                ->willReturn($mockSubjectConfigurations);

        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // expect the unlock method
        $this->subject->expects($this->once())
                      ->method('unlock')
                      ->willReturn(null);
        $this->subject->expects($this->once())
                      ->method('removeLineFromFile')
                      ->willReturn(null);

        // invoke the process() method
        $this->subject->process();
    }

    /**
     * Tests's the plugin's process method with a subject and an invalid source directory resulting in an exception.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Source directory /path/to/nowhere for subject a.subject.id is not available!
     */
    public function testProcessWithOneSubjectAndInvalidSourceDirAndException()
    {

        // mock tha basic data
        $serial = uniqid();
        $status = array(RegistryKeys::SOURCE_DIRECTORY => '/path/to/nowhere');

        // mock the subject
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getPrefix')
                                 ->willReturn($prefix = 'product-import');
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getId')
                                 ->willReturn('a.subject.id');

        // mock the array with subjects
        $mockSubjectConfigurations = array($mockSubjectConfiguration);

        // mock the system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->once())
                              ->method('mergeAttributesRecursive')
                              ->with($serial, array($prefix => array()))
                              ->willReturn(null);
        $mockRegistryProcessor->expects($this->once())
                              ->method('getAttribute')
                              ->with($serial)
                              ->willReturn($status);

        // mock the application methods
        $this->mockApplication->expects($this->exactly(2))
                              ->method('getRegistryProcessor')
                              ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->exactly(3))
                              ->method('getSerial')
                              ->willReturn($serial);
        $this->mockApplication->expects($this->any())
                              ->method('getSystemLogger')
                              ->willReturn($mockSystemLogger);

        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();
        $mockPluginConfiguration->expects($this->once())
                                ->method('getSubjects')
                                ->willReturn($mockSubjectConfigurations);

        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // expect the unlock method
        $this->subject->expects($this->once())
                      ->method('unlock')
                      ->willReturn(null);

        // invoke the process() method
        $this->subject->process();
    }

    /**
     * Test's if the passed file is NOT part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithNoBunch()
    {

        // initialize the prefix and the actual date
        $suffix = 'csv';
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $suffix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-173_01.csv', $prefix, $suffix, $actualDate), false),
            array(sprintf('import/add-update/%s_%s-174_01.csv', $prefix, $suffix, $actualDate), false),
        );

        // make the protected method accessible
        $reflectionObject = new \ReflectionObject($this->subject);
        $reflectionMethod = $reflectionObject->getMethod('isPartOfBunch');
        $reflectionMethod->setAccessible(true);


        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();

        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // make sure, that only the FIRST file is part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $suffix, $filename));
        }
    }

    /**
     * Test's if the passed file IS part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithBunch()
    {

        // initialize the prefix and the actual date
        $suffix = 'csv';
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $suffix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_02.csv', $prefix, $suffix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_03.csv', $prefix, $suffix, $actualDate), true),
        );

        // make the protected method accessible
        $reflectionObject = new \ReflectionObject($this->subject);
        $reflectionMethod = $reflectionObject->getMethod('isPartOfBunch');
        $reflectionMethod->setAccessible(true);


        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();

        // set the plugin configuration
        $this->subject->setPluginConfiguration($mockPluginConfiguration);

        // make sure, that the file IS part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $suffix, $filename));
        }
    }
}
