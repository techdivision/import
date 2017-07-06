<?php

/**
 * TechDivision\Import\Subjects\AbstractSubjectTest
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

use TechDivision\Import\Utils\LoggerKeys;

/**
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractSubjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract subject that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\AbstractSubject
     */
    protected $abstractSubject;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        $mockLoggers = array(
            LoggerKeys::SYSTEM => $this->getMockBuilder('Psr\Log\LoggerInterface')
                                       ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                       ->getMock()
        );

        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();

        $mockGenerator = $this->getMockBuilder('TechDivision\Import\Utils\Generators\GeneratorInterface')
                              ->setMethods(get_class_methods('TechDivision\Import\Utils\Generators\GeneratorInterface'))
                              ->getMock();

        $args = array(
            $mockRegistryProcessor,
            $mockGenerator,
            $mockLoggers
        );

        // create a mock subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();

        // initialize the abstract subject that has to be tested
        $this->abstractSubject = $this->getMockBuilder('TechDivision\Import\Subjects\AbstractSubject')
                                      ->setConstructorArgs($args)
                                      ->setMethods(array('isFile', 'touch', 'rename', 'write'))
                                      ->getMockForAbstractClass();

        // set the mock configuration instance
        $this->abstractSubject->setConfiguration($mockSubjectConfiguration);
    }

    /**
     * Test's if the getHeaders() method returns the given headers.
     *
     * @return void
     */
    public function testSetGetHeaders()
    {
        $this->abstractSubject->setHeaders(array($headerName = 'test' => 0));
        $this->assertCount(1, $headers = $this->abstractSubject->getHeaders());
        $this->assertArrayHasKey($headerName, $headers);
        $this->assertSame(0, $headers[$headerName]);
    }

    /**
     * Test's if the add/has/getHeader() methods returns the apropriate values.
     *
     * @return void
     */
    public function testAddHasGetHeaderWithMultipleHeaders()
    {
        $this->abstractSubject->addHeader($headerName0 = 'test-00');
        $this->abstractSubject->addHeader($headerName1 = 'test-01');
        $this->assertTrue($this->abstractSubject->hasHeader($headerName0));
        $this->assertTrue($this->abstractSubject->hasHeader($headerName1));
        $this->assertSame(0, $this->abstractSubject->getHeader($headerName0));
        $this->assertSame(1, $this->abstractSubject->getHeader($headerName1));
    }

    /**
     * Test's if the getHeader() method throwns the expected exception when requesting an invalid header name.
     *
     * @return void
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Header unknown is not available
     */
    public function testGetInvalidHeader()
    {
        $this->abstractSubject->getHeader('unknown');
    }

    /**
     * Test the set/getValue() method.
     *
     * @return void
     */
    public function testGetValueWithNumber()
    {

        // mock the available haeders
        $this->abstractSubject->setHeaders(array($name = 'test' => 0));

        // query whether or not the given value is available and a number
        $this->abstractSubject->setValue($name, $value = 1);
        $this->assertSame($value, $this->abstractSubject->getValue($name));
    }

    /**
     * Test the set/getValue() method converting the value by invoking a callback.
     *
     * @return void
     */
    public function testGetValueWithStringConvertedToNumberByCallback()
    {

        // mock the available haeders
        $this->abstractSubject->setHeaders(array($name = 'test' => 0));

        // query whether or not the given value is available and a number
        $this->abstractSubject->setValue($name, '100');
        $this->assertSame(100, $this->abstractSubject->getValue($name, null, function ($value) {
            return (integer) $value;
        }));
    }

    /**
     * Test the getValue() method with a default value.
     *
     * @return void
     */
    public function testGetDefaultValue()
    {
        $this->assertSame(100, $this->abstractSubject->getValue('test', 100));
    }

    /**
     * Test the hasValue() method without a header available.
     *
     * @return void
     */
    public function testHasValueWithMissingValueWithoutHeader()
    {
        $this->assertFalse($this->abstractSubject->hasValue('test'));
    }

    /**
     * Test the hasValue() method with a header available.
     *
     * @return void
     */
    public function testHasValueWithMissingValueWithHeader()
    {
        $this->abstractSubject->addHeader($name = 'test');
        $this->assertFalse($this->abstractSubject->hasValue($name));
    }

    /**
     * Test the formatDate() method with a valid date.
     *
     * @return void
     */
    public function testFormatDateWithValidDate()
    {

        // mock the source date format
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getSourceDateFormat')
             ->willReturn('n/d/y, g:i A');


        // query whether or not the date is formatted as expected
        $this->assertSame('2016-10-24 17:36:00', $this->abstractSubject->formatDate('10/24/16, 5:36 PM'));
    }

    /**
     * Test the formatDate() method with an invalid date.
     *
     * @return void
     */
    public function testFormatDateWithInvalidDate()
    {

        // mock the source date format
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getSourceDateFormat')
             ->willReturn('n/d/y, g:i A');


        // make sure that NULL is returned for an invalid date
        $this->assertNull($this->abstractSubject->formatDate('invalid date'));
    }

    /**
     * Test the import() method with a not matching filename.
     *
     * @return void
     */
    public function testImportWithNotMatchingFilename()
    {

        // mock the prefix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getPrefix')
             ->willReturn('product-import');

        // mock the suffix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getSuffix')
             ->willReturn('csv');

        $this->assertNull($this->abstractSubject->import(uniqid(), 'var/importexport/test.xxx'));
    }

    /**
     * Test the import() method with a not matching filename.
     *
     * @return void
     */
    public function testImportWithMatchingFilename()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // create the mock import adapter and mock the import() method
        $mockImportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ImportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ImportAdapterInterface'))
                                  ->getMock();
        $mockImportAdapter->expects($this->once())
                          ->method('import')
                          ->willReturn(null);

        // set the import adapter
        $this->abstractSubject->setImportAdapter($mockImportAdapter);

        // mock the prefix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getPrefix')
             ->willReturn('product-import');

        // mock the suffix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getSuffix')
             ->willReturn('csv');

        // mock the isFile() method
        $this->abstractSubject
             ->expects($this->exactly(3))
             ->method('isFile')
             ->willReturn(false);

        // mock the touch() method
        $this->abstractSubject
             ->expects($this->once())
             ->method('touch')
             ->with(sprintf('%s.inProgress', $filename))
             ->willReturn(true);

        // mock the rename() method
        $this->abstractSubject
             ->expects($this->once())
             ->method('rename')
             ->with(sprintf('%s.inProgress', $filename), sprintf('%s.imported', $filename))
             ->willReturn(true);

        // try to import the file with the passed name
        $this->assertNull($this->abstractSubject->import($serial = uniqid(), $filename));
    }

    /**
     * Test the import() method with a not matching filename throwing an exception.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong
     */
    public function testImportWithMatchingFilenameAndException()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // create the mock import adapter and mock the import() method
        $mockImportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ImportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ImportAdapterInterface'))
                                  ->getMock();
        $mockImportAdapter->expects($this->once())
                          ->method('import')
                          ->willThrowException(new \Exception('Something went wrong'));

        // set the import adapter
        $this->abstractSubject->setImportAdapter($mockImportAdapter);

        // mock the prefix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getPrefix')
             ->willReturn('product-import');

        // mock the suffix
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getSuffix')
             ->willReturn('csv');

        // mock the isFile() method
        $this->abstractSubject
             ->expects($this->exactly(3))
             ->method('isFile')
             ->willReturn(false);

        // mock the touch() method
        $this->abstractSubject
             ->expects($this->once())
             ->method('touch')
             ->with(sprintf('%s.inProgress', $filename))
             ->willReturn(true);

        // try to import the file with the passed name
        $this->abstractSubject->import($serial = uniqid(), $filename);
    }
}
