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

use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Exceptions\WrappedColumnException;

/**
 * Test class for the abstract subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractSubjectTest extends AbstractTest
{

    /**
     * The abstract subject that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\AbstractSubject
     */
    protected $abstractSubject;

    /**
     * The serial used to setup the subject.
     *
     * @var string
     */
    protected $serial;

    /**
     * The class name of the subject we want to test.
     *
     * @return string The class name of the subject
     */
    protected function getSubjectClassName()
    {
        return 'TechDivision\Import\Subjects\AbstractSubject';
    }

    /**
     * Return the subject's methods we want to mock.
     *
     * @return array The methods
     */
    protected function getSubjectMethodsToMock()
    {
        return array(
            'touch',
            'write',
            'rename',
            'isFile',
            'getHeaderMappings',
            'getDefaultCallbackMappings'
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        // create the subject instance we want to test and invoke the setup method
        $this->abstractSubject = $this->getSubjectInstance();
        $this->abstractSubject->setUp($this->serial = uniqid());
    }

    /**
     * The the tearDown() method.
     *
     * @return void
     */
    public function testTearDown()
    {

        // mock the method returning the new source directory
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getTargetDir')
             ->willReturn($targetDir = 'var/importexport');

        // mock the attribute merging
        $this->abstractSubject
             ->getRegistryProcessor()
             ->expects($this->once())
             ->method('mergeAttributesRecursive')
             ->with(
                 $this->serial,
                 array(RegistryKeys::SOURCE_DIRECTORY => sprintf('%s/%s', $targetDir, $this->serial))
             )
             ->willReturn(null);

        // invoke the tear down and make sure no value will be returned
        $this->assertNull($this->abstractSubject->tearDown($this->serial));
    }

    /**
     * Test the getSerial() method.
     *
     * @return void
     */
    public function testSetGetSerial()
    {
        $this->abstractSubject->setSerial($this->serial);
        $this->assertSame($this->serial, $this->abstractSubject->getSerial());
    }

    /**
     * The the getCallbackMappings() method with overwritten callbacks.
     *
     * @return void
     */
    public function testGetCallbackMappings()
    {

        // load the callback mappings
        $callbackMappings = $this->abstractSubject->getCallbackMappings();

        // make sure callback mapping have been overwritten
        $this->assertSame(
            array('import.test.callback-01.id'),
            $callbackMappings['attribute_code']
        );
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
     * Test the import() method with a matching filename.
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
             ->withConsecutive(
                 array(sprintf('%s.failed', $filename)),
                 array(sprintf('%s.imported', $filename)),
                 array(sprintf('%s.inProgress', $filename))
             )
             ->willReturnOnConsecutiveCalls(false, false, false);

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
        $this->assertNull($this->abstractSubject->import(uniqid(), $filename));
    }

    /**
     * Test the import() method with a matching filename throwing an exception.
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
             ->withConsecutive(
                 array(sprintf('%s.failed', $filename)),
                 array(sprintf('%s.imported', $filename)),
                 array(sprintf('%s.inProgress', $filename))
             )
             ->willReturnOnConsecutiveCalls(false, false, false);

        // mock the touch() method
        $this->abstractSubject
             ->expects($this->once())
             ->method('touch')
             ->with(sprintf('%s.inProgress', $filename))
             ->willReturn(true);

        // try to import the file with the passed name
        $this->abstractSubject->import(uniqid(), $filename);
    }

    /**
     * Test the import() method with a matching filename throwing an WrappedColunException.
     *
     * @return void
     *
     * @expectedException \TechDivision\Import\Exceptions\WrappedColumnException
     * @expectedExceptionMessage Something went wrong
     */
    public function testImportWithMatchingFilenameAndWrappedException()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // create the mock import adapter and mock the import() method
        $mockImportAdapter = $this->getMockBuilder('TechDivision\Import\Adapter\ImportAdapterInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Adapter\ImportAdapterInterface'))
                                  ->getMock();
        $mockImportAdapter->expects($this->once())
                          ->method('import')
                          ->willThrowException(new WrappedColumnException('Something went wrong'));

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
             ->withConsecutive(
                 array(sprintf('%s.failed', $filename)),
                 array(sprintf('%s.imported', $filename)),
                 array(sprintf('%s.inProgress', $filename))
             )
             ->willReturnOnConsecutiveCalls(false, false, false);

        // mock the touch() method
        $this->abstractSubject
             ->expects($this->once())
             ->method('touch')
             ->with(sprintf('%s.inProgress', $filename))
             ->willReturn(true);

        // try to import the file with the passed name
        $this->abstractSubject->import(uniqid(), $filename);
    }

    /**
     * Test the import() method with a matching filename and an existing .inProgress flag file.
     *
     * @return void
     */
    public function testImportWithMatchingFilenameButExistingInProgressFile()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

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
             ->expects($this->once())
             ->method('isFile')
             ->with(sprintf('%s.failed', $filename))
             ->willReturn(true);

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Import running, found inProgress file %s', sprintf('%s.inProgress', $filename)))
             ->willReturn(null);

        // try to import the file with the passed name
        $this->abstractSubject->import(uniqid(), $filename);
    }

    /**
     * Test the importRow() method.
     *
     * @return void
     */
    public function testImportRow()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename and headers
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(array('col1' => 0, 'col2' => 1));

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Successfully processed operation "add-update" in file %s on line 1', $filename))
             ->willReturn(null);

        // create a mock observer
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->once())
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100));

        // register a mock observer
        $this->abstractSubject->registerObserver($mockObserver, 'import');

        // start importing the row
        $this->abstractSubject->importRow($row);

        // query whether or not the row have been initialized
        $this->assertSame($row, $this->abstractSubject->getRow());
    }

    /**
     * Test the importRow() method and skip processing.
     *
     * @return void
     */
    public function testImportRowAndSkip()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename, headers and skip the first row
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(array('col1' => 0, 'col2' => 1));

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Successfully processed operation "add-update" in file %s on line 1', $filename))
             ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(0))
                     ->method('handle');

        // register the mock observers
        $this->abstractSubject->registerObserver(new SkipObserverImpl(), 'import');
        $this->abstractSubject->registerObserver($mockObserver, 'import');

        // start importing the row
        $this->abstractSubject->importRow($row = array(0 => 'value1', 1 => 100));

        // query whether or not the row have been initialized
        $this->assertSame($row, $this->abstractSubject->getRow());
    }

    /**
     * Test the importRow() method with the header line.
     *
     * @return void
     */
    public function testImportHeaderRow()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename and headers
        $this->abstractSubject->setFilename($filename);

        // start importing the row
        $this->abstractSubject->importRow(array(0 => 'col1', 1 => 'col2'));

        // query whether or not the headers have been initialized
        $this->assertSame(array('col1' => 0, 'col2' => 1), $this->abstractSubject->getHeaders());
    }

    /**
     * Test the mapAttributeCodeByHeaderMapping() method.
     *
     * @return void
     */
    public function testMapAttributeCodeByHeaderMapping()
    {

        // prepare the header mappings
        $this->abstractSubject
             ->expects($this->once())
             ->method('getHeaderMappings')
             ->willReturn(array($attributeCode = 'attributeCode' => $mappedName = 'mappedName'));

        // query whether the header will be mapped or not
        $this->assertSame($mappedName, $this->abstractSubject->mapAttributeCodeByHeaderMapping($attributeCode));
    }

    /**
     * Test the getCoreConfigData() method.
     *
     * @return void
     */
    public function testGetCoreConfigData()
    {
        $this->assertSame(1, $this->abstractSubject->getCoreConfigData('web/seo/use_rewrites'));
    }

    /**
     * Test the getCoreConfigData() method throwing an exception.
     *
     * @return void
     */
    public function testGetCoreConfigDataWithDefaultValue()
    {
        $this->assertSame(1001, $this->abstractSubject->getCoreConfigData('unknown/config/value', 1001));
    }

    /**
     * Test the getCoreConfigData() method with specific scope.
     *
     * @return void
     */
    public function testGetCoreConfigDataWithSpecificScope()
    {

        // try to load the configuration value
        $configValue = $this->abstractSubject
                            ->getCoreConfigData(
                                'fallback/on/website/level',
                                null,
                                ScopeKeys::SCOPE_STORES,
                                1
                            );

        // query whether or not the requested value has been found
        $this->assertSame(1002, $configValue);
    }

    /**
     * Test the getCoreConfigData() method with fallback on default level.
     *
     * @return void
     */
    public function testGetCoreConfigDataWithFallbackOnDefaultLevel()
    {

        // try to load the configuration value
        $configValue = $this->abstractSubject
                            ->getCoreConfigData(
                                'fallback/on/default/level',
                                null,
                                ScopeKeys::SCOPE_STORES,
                                1
                            );

        // query whether or not the requested value has been found
        $this->assertSame(1001, $configValue);
    }

    /**
     * Test the getCoreConfigData() method throwing an exception.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Can't find a value for configuration "unknown/config/value-default-0" in "core_config_data"
     */
    public function testGetCoreConfigDataWithException()
    {
        $this->abstractSubject->getCoreConfigData('unknown/config/value');
    }

    /**
     * Test if an exception is thrown when a invalid logger is requested.
     *
     * @return
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The requested logger 'invalid-logger-name' is not available
     */
    public function testGetSystemLoggerWithException()
    {
        $this->abstractSubject->getSystemLogger('invalid-logger-name');
    }

    /**
     * Test the getSystemLoggers() method.
     *
     * @return void
     */
    public function testGetSystemLoggers()
    {
        $systemLoggers = $this->abstractSubject->getSystemLoggers();
        $this->assertCount(1, $systemLoggers);
        $this->assertArrayHasKey(LoggerKeys::SYSTEM, $systemLoggers);
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $systemLoggers[LoggerKeys::SYSTEM]);
    }

    /**
     * Test the wrapException() method.
     *
     * @return void
     */
    public function testWrapException()
    {

        // set the filename and the original filename
        $filename = 'var/importexport/variants_20170706-160000_01.csv';
        $originalFilename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename and headers
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(
                                    array(
                                        $colunmName1 = 'col1'     => 0,
                                        $columnName2 = 'col2'     => 1,
                                        ColumnKeys::ORIGINAL_DATA => 2
                                    )
                                );

        // create a mock observer
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->once())
                     ->method('handle')
                     ->willReturn(
                         $row = array(
                             0 => 'value1',
                             1 => 100,
                             2 => serialize(
                                 array(
                                     ColumnKeys::ORIGINAL_FILENAME => $originalFilename,
                                     ColumnKeys::ORIGINAL_LINE_NUMBER => $originalLineNumber = 100,
                                     ColumnKeys::ORIGINAL_COLUMN_NAMES => array(
                                                                              $colunmName1 => $originalColumnName1 = 'origCol1',
                                                                              '*'          => $originalColumnName2 = 'origCol2'
                                                                          )
                                 )
                             )
                         )
                     );

        // register a mock observer
        $this->abstractSubject->registerObserver($mockObserver, 'import');

        // start importing the row
        $this->abstractSubject->importRow($row);

        // query whether or not the row have been initialized
        $this->assertSame($row, $this->abstractSubject->getRow());

        // wrap the exception
        $wrappedException = $this->abstractSubject
                                 ->wrapException(
                                     array($colunmName1, $columnName2),
                                     new \Exception('Something went wrong')
                                 );

        // query whether or not a wrapped exception with an apropriate message has been created
        $this->assertInstanceOf('TechDivision\Import\Exceptions\WrappedColumnException', $wrappedException);
        $this->assertSame(
            sprintf(
                'Something went wrong in file %s on line %d in column(s) %s, %s',
                $originalFilename,
                $originalLineNumber,
                $originalColumnName1,
                $originalColumnName2
            ),
            $wrappedException->getMessage()
        );
    }

    /**
     * Test the resolveOriginalColumnName() method without original data available.
     *
     * @return void
     */
    public function testResolveOriginalColumnNameWithEmptyOriginalData()
    {
        $this->assertSame('columnName', $this->abstractSubject->resolveOriginalColumnName('columnName'));
    }

    /**
     * Test the set/getStoreViewCode() method with a value set.
     *
     * @return
     */
    public function testGetStoreViewCodeEmpty()
    {
        $this->assertSame('default', $this->abstractSubject->getStoreViewCode());
    }

    /**
     * Test the set/getStoreViewCode() method without a default value.
     *
     * @return
     */
    public function testSetGetStoreViewCode()
    {

        // set a store view code and query if it will be returned
        $this->abstractSubject->setStoreViewCode(StoreViewCodes::ADMIN);
        $this->assertSame(StoreViewCodes::ADMIN, $this->abstractSubject->getStoreViewCode());
    }

    /**
     * Test the set/getStoreViewCode() method without a default value.
     *
     * @return
     */
    public function testGetStoreViewCodeWithDefaultValue()
    {

        // initialize the default value
        $default = 'test';

        // query whether or not the default value is returned
        $this->assertSame($default, $this->abstractSubject->getStoreViewCode($default));
    }

    /**
     * Test the prepareStoreViewCode() method.
     *
     * @return void
     */
    public function testPrepareStoreViewCode()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename, headers and skip the first row
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(array('col1' => 0, 'col2' => 1, ColumnKeys::STORE_VIEW_CODE => 2));

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Successfully processed operation "add-update" in file %s on line 1', $filename))
             ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(1))
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100, 2 => $storeViewCode = 'en_US'));

        // register the mock observers
        $this->abstractSubject->registerObserver($mockObserver, 'import');

        // start importing the row
        $this->abstractSubject->importRow($row);
        $this->abstractSubject->prepareStoreViewCode();

        // query whether or not the store view code has been prepared
        $this->assertSame($storeViewCode, $this->abstractSubject->getStoreViewCode());
    }

    /**
     * Test the getStoreId() method.
     *
     * @return void
     */
    public function testGetStoreId()
    {
        $this->assertSame(1, $this->abstractSubject->getStoreId('default'));
    }

    /**
     * Test getStoreId() with an invalid store view code.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Found invalid store view code unknown in file var/importexport/product-import_20170706-160000_01.csv on line 1
     */
    public function testGetStoreIdWithException()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename, headers and skip the first row
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(array('col1' => 0, 'col2' => 1, ColumnKeys::STORE_VIEW_CODE => 2));

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Successfully processed operation "add-update" in file %s on line 1', $filename))
             ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(1))
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100, 2 => 'en_US'));

        // register the mock observers
        $this->abstractSubject->registerObserver($mockObserver, 'import');

        // start importing the row
        $this->abstractSubject->importRow($row);

        // try to load an unknown store view code
        $this->abstractSubject->getStoreId('unknown');
    }

    /**
     * Test the getRowStoreId() method with an existing row.
     *
     * @return
     */
    public function testGetRowStoreIdWithRow()
    {

        // set the filename
        $filename = 'var/importexport/product-import_20170706-160000_01.csv';

        // set filename, headers and skip the first row
        $this->abstractSubject->setFilename($filename);
        $this->abstractSubject->setHeaders(array('col1' => 0, 'col2' => 1, ColumnKeys::STORE_VIEW_CODE => 2));

        // mock the system loggers debug() method
        $this->abstractSubject
             ->getSystemLogger()
             ->expects($this->once())
             ->method('debug')
             ->with(sprintf('Successfully processed operation "add-update" in file %s on line 1', $filename))
             ->willReturn(null);

        // register the mock observers
        $this->abstractSubject->registerObserver(new PrepareStoreViewCodeObserverImpl(), 'import');

        // start importing the row
        $this->abstractSubject->importRow(array(0 => 'value1', 1 => 100, 2 => 'en_US'));

        // make sure the store view code related store ID has been returned
        $this->assertSame(2, $this->abstractSubject->getRowStoreId());
    }

    /**
     * Test the getRowStoreId() method without an existing row.
     *
     * @return
     */
    public function testGetRowStoreIdWithoutRow()
    {
        $this->assertSame(1, $this->abstractSubject->getRowStoreId());
    }

    /**
     * Test the getRootCategory() method.
     *
     * @return void
     */
    public function testGetRootCategory()
    {

        // initialize the root category
        $rootCategory = array(
            MemberNames::ENTITY_ID => 2,
            MemberNames::PATH => '1/2'
        );

        // query whether or not the root category is available
        $this->assertSame($rootCategory, $this->abstractSubject->getRootCategory());
    }

    /**
     * Test the getRootCategory() method throwing an exception.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Root category for unknown is not available
     */
    public function testGetRootCategoryWithException()
    {
        $this->abstractSubject->setStoreViewCode('unknown');
        $this->abstractSubject->getRootCategory();
    }

    /**
     * Test the raise counter method.
     *
     * @return void
     */
    public function testRaiseCounter()
    {

        // mock the registry processor raiseCounter() method
        $this->abstractSubject
             ->getRegistryProcessor()
             ->expects($this->once())
             ->method('raiseCounter')
             ->with($this->serial, $counterName = 'testCounter')
             ->willReturn($newCounterValue = 1);

        // set the serial first
        $this->abstractSubject->setSerial($this->serial);

        // raise the counter and test the result
        $this->assertSame($newCounterValue, $this->abstractSubject->raiseCounter($counterName));
    }

    /**
     * Test the recursive merge attributes method.
     *
     * @return void
     */
    public function testMergeAttributesRecursive()
    {

        // mock the registry processor raiseCounter() method
        $this->abstractSubject
             ->getRegistryProcessor()
             ->expects($this->once())
             ->method('mergeAttributesRecursive')
             ->with($this->serial, $status = array('test' => 'test'))
             ->willReturn(null);

        // set the serial first
        $this->abstractSubject->setSerial($this->serial);

        // merge the attributes recursively
        $this->assertNull($this->abstractSubject->mergeAttributesRecursive($status));
    }

    /**
     * Test the explode() method.
     *
     * @return void
     */
    public function testExplodeWithSimpleString()
    {

        // mock the CSV configuration options
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getDelimiter')
             ->willReturn(',');
       $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEnclosure')
             ->willReturn('"');
       $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEscape')
             ->willReturn('"');

        // explode a simple string
        $this->assertSame(array('foo', 'bar'), $this->abstractSubject->explode('foo,bar'));
    }

    /**
     * Test the explode() method with an escaped string.
     *
     * @return void
     */
    public function testExplodeWithEscapedString()
    {

        // mock the CSV configuration options
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getDelimiter')
             ->willReturn(',');
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEnclosure')
             ->willReturn('"');
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEscape')
             ->willReturn('"');

        // explode the escaped string
        $this->assertSame(array('foo,bar', 'bar,foo'), $this->abstractSubject->explode('"foo,bar","bar,foo"'));
    }

    /**
     * Test the isDebugMode() method.
     *
     * @return void
     */
    public function testIsDebugMode()
    {

        // mock the debug mode configuration value
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('isDebugMode')
             ->willReturn(true);

        // query whether or not the debug mode has been set
        $this->assertTrue($this->abstractSubject->isDebugMode());
    }

    /**
     * Test the getMultipleFieldDelimiter() method.
     *
     * @return void
     */
    public function testGetMultipleFieldDelimiter()
    {

        // mock the multiple field delimiter configuration value
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getMultipleFieldDelimiter')
             ->willReturn($multipleFieldDelimiter = ',');

             // query whether or not the multiple field delimiter is returned
             $this->assertSame($multipleFieldDelimiter, $this->abstractSubject->getMultipleFieldDelimiter());
    }

    /**
     * Test the getMultipleValueDelimiter() method.
     *
     * @return void
     */
    public function testGetMultipleValueDelimiter()
    {

        // mock the multiple value delimiter configuration value
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getMultipleValueDelimiter')
             ->willReturn($multipleValueDelimiter = '|');

        // query whether or not the multiple value delimiter is returned
        $this->assertSame($multipleValueDelimiter, $this->abstractSubject->getMultipleValueDelimiter());
    }

    /**
     * Test the isOkFileNeeded() method.
     *
     * @return void
     */
    public function testIsOkFileNeeded()
    {

        // mock the OK file needed configuration value
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('isOkFileNeeded')
             ->willReturn(true);

        // query whether or not an OK file is needed
        $this->assertTrue($this->abstractSubject->isOkFileNeeded());
    }

    /**
     * Query the set/getOperationName() method.
     *
     * @return void
     */
    public function testSetGetOperationName()
    {
        $this->abstractSubject->setOperationName($operationName = 'add-update');
        $this->assertSame($operationName, $this->abstractSubject->getOperationName());
    }

    /**
     * Query the set/getLineNumber() method.
     *
     * @return void
     */
    public function testSetGetLineNumber()
    {
        $this->abstractSubject->setLineNumber($lineNumber = 3);
        $this->assertSame($lineNumber, $this->abstractSubject->getLineNumber());
    }

    /**
     * Test the getCallbacks() method.
     *
     * @return void
     */
    public function testGetCallbacks()
    {

        // create a mock callback
        $mockCallback = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackInterface'))
                             ->getMock();

        // register a mock callback
        $this->abstractSubject->registerCallback($mockCallback, $type = 'import');

        // assert that the callbacks have been registered
        $this->assertSame(
            array($type => array($mockCallback)),
            $this->abstractSubject->getCallbacks()
        );
    }

    /**
     * Test the getCallbacksByType() method.
     *
     * @return void
     */
    public function testGetCallbacksByType()
    {

        // create a mock callback
        $mockCallback = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackInterface'))
                             ->getMock();

        // register a mock callback
        $this->abstractSubject->registerCallback($mockCallback, $type = 'import');

        // assert that the callbacks have been registered
        $this->assertSame(
            array($mockCallback),
            $this->abstractSubject->getCallbacksByType($type)
        );
    }

    /**
     * Test the getDefaultCallbackMappings() method.
     *
     * @return void
     */
    public function testGetDefaultCallbackMappings()
    {

        // initialize the abstract subject that has to be tested
        $abstractSubject = $this->getMockBuilder('TechDivision\Import\Subjects\AbstractSubject')
                                ->setConstructorArgs($this->getMockSubjectConstructorArgs())
                                ->getMockForAbstractClass();

        // make sure the default callback mappings are an empty array
        $this->assertCount(0, $abstractSubject->getDefaultCallbackMappings());
    }

    /**
     * Test the getTarget() method.
     *
     * @return void
     */
    public function testGetTarget()
    {

        // mock the target directory configuration value
        $this->abstractSubject
             ->getConfiguration()
             ->expects($this->once())
             ->method('getTargetDir')
             ->willReturn($targetDir = 'var/importexport');

        // set the serial
        $this->abstractSubject->setSerial($this->serial);

        // query whether or not the expected target directory is returned
        $this->assertSame(sprintf('%s/%s', $targetDir, $this->serial), $this->abstractSubject->getTargetDir());
    }
}
