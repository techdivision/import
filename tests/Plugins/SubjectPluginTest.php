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
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Plugins\SubjectPlugin
     */
    protected $subject;

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

        // create a mock callback visitor
        $mockCallbackVisitor = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackVisitor')
                                    ->disableOriginalConstructor()
                                    ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackVisitor'))
                                    ->getMock();

        // create a mock observer visitor
        $mockObserverVisitor = $this->getMockBuilder('TechDivision\Import\Observers\ObserverVisitor')
                                    ->disableOriginalConstructor()
                                    ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverVisitor'))
                                    ->getMock();

        // create a mock subject factory
        $mockSubjectFactory = $this->getMockBuilder('TechDivision\Import\Subjects\SubjectFactoryInterface')
                                   ->setMethods(get_class_methods('TechDivision\Import\Subjects\SubjectFactoryInterface'))
                                   ->getMock();

        // initialize the subject instance
        $this->subject = new SubjectPlugin($this->mockApplication, $mockCallbackVisitor, $mockObserverVisitor, $mockSubjectFactory);
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
     * Test's if the passed file is NOT part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithNoBunch()
    {

        // initialize the prefix and the actual date
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-173_01.csv', $prefix, $actualDate), false),
            array(sprintf('import/add-update/%s_%s-174_01.csv', $prefix, $actualDate), false),
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
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $filename));
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
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_02.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_03.csv', $prefix, $actualDate), true),
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
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $filename));
        }
    }
}
