<?php

/**
 * TechDivision\Import\Subjects\SubjectFactoryTest
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

/**
 * Test class for the subject factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the createSubject() method.
     *
     * @return void
     */
    public function testCreateSubject()
    {

        // mock the import adapter configuration
        $mockImportAdapterConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface')
                                               ->setMethods(get_class_methods('TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface'))
                                               ->getMock();
        $mockImportAdapterConfiguration->expects($this->once())
                                       ->method('getId')
                                       ->willReturn($importAdapterId = 'import.a.random.import.adapter.id');

        // mock the export adapter configuration
        $mockExportAdapterConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface')
                                               ->setMethods(get_class_methods('TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface'))
                                               ->getMock();
        $mockExportAdapterConfiguration->expects($this->once())
                                       ->method('getId')
                                       ->willReturn($exportAdapterId = 'import.a.random.export.adapter.id');

        // mock the subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getId')
                                 ->willReturn($subjectId = 'import.a.random.subject.id');
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getImportAdapter')
                                 ->willReturn($mockImportAdapterConfiguration);
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getExportAdapter')
                                 ->willReturn($mockExportAdapterConfiguration);

        // mock the import adapter
        $mockImportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ImportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ImportAdapterInterface'))
                                  ->getMock();

        // mock the export adapter
        $mockExportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ExportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ExportAdapterInterface'))
                                  ->getMock();

        // mock the subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Subjects\ExportableTraitImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Subjects\ExportableTraitImpl'))
                            ->getMockForAbstractClass();
        $mockSubject->expects($this->once())
                    ->method('setConfiguration')
                    ->with($mockSubjectConfiguration)
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('setImportAdapter')
                    ->with($mockImportAdapter)
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('setExportAdapter')
                    ->with($mockExportAdapter)
                    ->willReturn(null);

        // mock the container
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                              ->setMethods(get_class_methods('Symfony\Component\DependencyInjection\ContainerInterface'))
                              ->getMock();
        $mockContainer->expects($this->exactly(3))
                      ->method('get')
                      ->withConsecutive(array($subjectId), array($importAdapterId), array($exportAdapterId))
                      ->willReturnOnConsecutiveCalls($mockSubject, $mockImportAdapter, $mockExportAdapter);

        // create the factory and a new subject instance
        $subjectFactory = new SubjectFactory($mockContainer);
        $this->assertSame($mockSubject, $subjectFactory->createSubject($mockSubjectConfiguration));
    }
}
