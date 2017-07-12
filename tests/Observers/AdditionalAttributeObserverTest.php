<?php

/**
 * TechDivision\Import\Observers\AdditionalAttributeObserverTest
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\ColumnKeys;

/**
 * Test class for the additional attribute observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The additional attribute observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AdditionalAttributeObserver
     */
    protected $additionalAttributeObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->additionalAttributeObserver = new AdditionalAttributeObserver();
    }

    /**
     * Test the handle() method.
     */
    public function testHandle()
    {

        // prepare the expected row structure after the observer
        $expectedRow = array(
            0 => $additionalAttributes = sprintf('%s=%s,%s=%s', $col1 = 'attr1', $val1 = 'val1', $col2 = 'attr2', $val2 = 'val2'),
            1 => $val1,
            2 => $val2
        );

        // create a dummy CSV file row
        $row = array(0 => $additionalAttributes);

        // create a mock configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\ConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->once())
                          ->method('getDelimiter')
                          ->willReturn(',');
        $mockConfiguration->expects($this->once())
                          ->method('getEnclosure')
                          ->willReturn('"');
        $mockConfiguration->expects($this->once())
                          ->method('getEscape')
                          ->willReturn('\\');

        // create a mock subject configuruation
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('getConfiguration')
                                 ->willReturn($mockConfiguration);

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->exactly(2))
                         ->method('debug')
                         ->withConsecutive(
                             array(
                                 sprintf(
                                     'Extract new column "%s" with value "%s" from column "%s" in file %s on line %d',
                                     $col1,
                                     $val1,
                                     ColumnKeys::ADDITIONAL_ATTRIBUTES,
                                     $filename = 'product-import_20170712-120012_01.csv',
                                     $lineNumber = 2
                                 )
                             ),
                             array(
                                 sprintf(
                                     'Extract new column "%s" with value "%s" from column "%s" in file %s on line %d',
                                     $col2,
                                     $val2,
                                     ColumnKeys::ADDITIONAL_ATTRIBUTES,
                                     $filename,
                                     $lineNumber
                                  )
                             )
                         )
                         ->willReturnOnConsecutiveCalls(null, null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Subjects\SubjectInterface')
                            ->setMethods(get_class_methods('TechDivision\Import\Subjects\SubjectInterface'))
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
        $mockSubject->expects($this->any())
                    ->method('isDebugMode')
                    ->willReturn(true);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->exactly(2))
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->exactly(2))
                    ->method('getFilename')
                    ->willReturn($filename);
        $mockSubject->expects($this->exactly(2))
                    ->method('getLineNumber')
                    ->willReturn($lineNumber);
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->withConsecutive(
                        array(ColumnKeys::ADDITIONAL_ATTRIBUTES),
                        array($col1),
                        array($col2)
                    )
                    ->willReturnOnConsecutiveCalls(true, false, false);
        $mockSubject->expects($this->exactly(3))
                    ->method('getHeader')
                    ->withConsecutive(
                        array(ColumnKeys::ADDITIONAL_ATTRIBUTES),
                        array($col1),
                        array($col2)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1, 2);
        $mockSubject->expects($this->exactly(2))
                    ->method('addHeader')
                    ->withConsecutive(
                        array($col1),
                        array($col2)
                     )
                    ->willReturn(0);
        $mockSubject->expects($this->exactly(2))
                    ->method('explode')
                    ->withConsecutive(
                        array(sprintf('%s=%s', $col1, $val1)),
                        array(sprintf('%s=%s', $col2, $val2))
                    )
                    ->willReturnOnConsecutiveCalls(
                        array($col1, $val1),
                        array($col2, $val2)
                    );

        // let the subject handle the additional attributes
        $this->assertSame($expectedRow, $this->additionalAttributeObserver->handle($mockSubject));
    }
}
