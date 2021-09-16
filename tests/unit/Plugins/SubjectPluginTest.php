<?php

/**
 * TechDivision\Import\Plugins\SubjectPluginTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Plugins;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Subjects\SubjectExecutorInterface;
use TechDivision\Import\Subjects\FileResolver\FileResolverInterface;
use TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface;

/**
 * Test class for the subject plugin implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectPluginTest extends TestCase
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
     * The mock file resolver factory instance.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFileResolverFactory;

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
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock application
        $this->mockApplication = $this->getMockBuilder(ApplicationInterface::class)->getMock();

        // create a mock subject executor
        $this->mockSubjectExecutor = $this->getMockBuilder(SubjectExecutorInterface::class)->getMock();

        // create a mock file resolver
        $this->mockFileResolverFactory = $this->getMockBuilder(FileResolverFactoryInterface::class)->getMock();

        // initialize the subject instance
        $this->subject = $this->getMockBuilder('TechDivision\Import\Plugins\SubjectPlugin')
                              ->setConstructorArgs(
                                  array(
                                      $this->mockApplication,
                                      $this->mockSubjectExecutor,
                                      $this->mockFileResolverFactory
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

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->exactly(2))
                              ->method('mergeAttributesRecursive')
                              ->withConsecutive(
                                  array(CacheKeys::STATUS, $status),
                                  array(CacheKeys::STATUS, array(RegistryKeys::BUNCHES => $bunches))
                              )
                              ->willReturn(null);

        // mock the configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Configuration\ConfigurationInterface'))
                                  ->getMock();

        // mock the application methods
        $this->mockApplication->expects($this->exactly(2))
                              ->method('getRegistryProcessor')
                              ->willReturn($mockRegistryProcessor);
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

        // mock the subject
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the array with subjects
        $mockSubjectConfigurations = array($mockSubjectConfiguration);

        // mock the subject factory
        $this->mockSubjectExecutor->expects($this->once())
            ->method('execute')
            ->willReturn(null);

        // initialize the mock file resolver instance
        $mockFileResolver = $this->getMockBuilder(FileResolverInterface::class)->getMock();

        // mock the file resolver methods
        $mockFileResolver->expects($this->once())
            ->method('loadFiles')
            ->willReturn(array(__DIR__ . DIRECTORY_SEPARATOR . '_file' . DIRECTORY_SEPARATOR . 'product-import_20170720-125052_01.csv'));
        $mockFileResolver->expects($this->once())
            ->method('getMatches')
            ->willReturn(array(array()));
        $mockFileResolver->expects($this->once())
            ->method('reset')
            ->willReturn(null);

        // let the mock file resolver factory create a mock file resolver instance
        $this->mockFileResolverFactory->expects($this->once())
            ->method('createFileResolver')
            ->willReturn($mockFileResolver);

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
            ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
            ->getMock();
        $mockRegistryProcessor->expects($this->exactly(2))
            ->method('mergeAttributesRecursive')
            ->willReturn(null);

        // mock the system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
            ->getMock();

        // mock the application methods
        $this->mockApplication->expects($this->exactly(2))
            ->method('getRegistryProcessor')
            ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->any())
            ->method('getSerial')
            ->willReturn(uniqid());
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

        // mock the subject
        $mockSubjectConfiguration = $this->getMockBuilder(SubjectConfigurationInterface::class)->getMock();

        // mock the array with subjects
        $mockSubjectConfigurations = array($mockSubjectConfiguration);

        // mock the subject factory
        $this->mockSubjectExecutor->expects($this->once())
                                  ->method('execute')
                                  ->willThrowException(new \Exception('Can\'t export file'));

        // initialize the mock file resolver instance
        $mockFileResolver = $this->getMockBuilder(FileResolverInterface::class)->getMock();

        // mock the file resolver methods
        $mockFileResolver->expects($this->once())
            ->method('loadFiles')
            ->willReturn(array(__DIR__ . DIRECTORY_SEPARATOR . '_file' . DIRECTORY_SEPARATOR . 'product-import_20170720-125052_01.csv'));
        $mockFileResolver->expects($this->once())
            ->method('getMatches')
            ->willReturn(array(array()));

        // let the mock file resolver factory create a mock file resolver instance
        $this->mockFileResolverFactory->expects($this->once())
            ->method('createFileResolver')
            ->willReturn($mockFileResolver);

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
            ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
            ->getMock();
        $mockRegistryProcessor->expects($this->once())
            ->method('mergeAttributesRecursive')
            ->willReturn(null);

        // mock the system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
            ->getMock();

        // mock the application methods
        $this->mockApplication->expects($this->once())
            ->method('getRegistryProcessor')
            ->willReturn($mockRegistryProcessor);
        $this->mockApplication->expects($this->any())
            ->method('getSerial')
            ->willReturn(uniqid());
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

        // invoke the process() method
        $this->subject->process();
    }
}
