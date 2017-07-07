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
use TechDivision\Import\Exceptions\WrappedColumnException;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\Generators\CoreConfigDataUidGenerator;
use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\StoreViewCodes;

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
     * The serial used to setup the subject.
     *
     * @var string
     */
    protected $serial;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // prepare the global data
        $globalData = array(
            RegistryKeys::GLOBAL_DATA => array(
                RegistryKeys::LINK_TYPES => array(),
                RegistryKeys::CATEGORIES => array(),
                RegistryKeys::TAX_CLASSES => array(),
                RegistryKeys::EAV_ATTRIBUTES => array(),
                RegistryKeys::ATTRIBUTE_SETS => array(),
                RegistryKeys::STORE_WEBSITES => array(
                    'admin' => array(
                        MemberNames::WEBSITE_ID => 0,
                        MemberNames::CODE => 'admin',
                        MemberNames::NAME => 'Admin'
                    ),
                    'base' => array(
                        MemberNames::WEBSITE_ID => 1,
                        MemberNames::CODE => 'base',
                        MemberNames::NAME => 'Main Website'
                    )
                ),
                RegistryKeys::DEFAULT_STORE => array(
                    MemberNames::STORE_ID => 1,
                    MemberNames::CODE => 'default',
                    MemberNames::WEBSITE_ID => 1
                ),
                RegistryKeys::ROOT_CATEGORIES => array(
                    'default' => array(
                        MemberNames::ENTITY_ID => 2,
                        MemberNames::PATH => '1/2'
                    )
                ),
                RegistryKeys::EAV_USER_DEFINED_ATTRIBUTES => array(),
                RegistryKeys::STORES => array(
                    'admin' => array(
                        MemberNames::STORE_ID => 0,
                        MemberNames::WEBSITE_ID => 0,
                        MemberNames::CODE => 'admin',
                        MemberNames::NAME => 'Admin'
                    ),
                    'default' => array(
                        MemberNames::STORE_ID => 1,
                        MemberNames::WEBSITE_ID => 1,
                        MemberNames::CODE => 'default',
                        MemberNames::NAME => 'Default Store View'
                    ),
                    'en_US' => array(
                        MemberNames::STORE_ID => 2,
                        MemberNames::WEBSITE_ID => 1,
                        MemberNames::CODE => 'en_US',
                        MemberNames::NAME => 'US Store'
                    )
                ),
                RegistryKeys::ENTITY_TYPES => array(
                    EntityTypeCodes::CATALOG_PRODUCT => array(
                        MemberNames::ENTITY_TYPE_ID => 4,
                        MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
                    )
                ),
                RegistryKeys::CORE_CONFIG_DATA => array(
                    'default/0/web/seo/use_rewrites' => array(
                        'config_id' => 1,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'web/seo/use_rewrites',
                        'value' => 1
                    ),
                    'default/0/web/unsecure/base_url' => array(
                        'config_id' => 2,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'web/unsecure/base_url',
                        'value' => 'http://127.0.0.1/magento2-ee-2.1.7-sampledata/'
                    ),
                    'default/0/web/secure/base_url' => array(
                        'config_id' => 3,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'web/secure/base_url',
                        'value' => 'https://127.0.0.1/magento2-ee-2.1.7-sampledata/'
                    ),
                    'default/0/general/locale/code' => array(
                        'config_id' => 4,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'general/locale/code',
                        'value' => 'en_US'
                    ),
                    'default/0/web/secure/use_in_frontend' => array(
                        'config_id' => 5,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'web/secure/use_in_frontend',
                        'value' => null
                    ),
                    'default/0/web/secure/use_in_adminhtml' => array(
                        'config_id' => 6,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'web/secure/use_in_adminhtml',
                        'value' => null
                    ),
                    'default/0/fallback/on/default/level' => array(
                        'config_id' => 7,
                        'scope' => 'default',
                        'scope_id' => 0,
                        'path' => 'fallback/on/website/level',
                        'value' => 1001
                    ),
                    'websites/1/fallback/on/website/level' => array(
                        'config_id' => 8,
                        'scope' => 'websites',
                        'scope_id' => 1,
                        'path' => 'fallback/on/website/level',
                        'value' => 1002
                    )
                )
            )
        );

        // mock the loggers
        $mockLoggers = array(
            LoggerKeys::SYSTEM => $this->getMockBuilder('Psr\Log\LoggerInterface')
                                       ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                       ->getMock()
        );

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();
        $mockRegistryProcessor->expects($this->once())
                              ->method('getAttribute')
                              ->willReturn($globalData);

        // mock the generator
        $mockGenerator = new CoreConfigDataUidGenerator();

        // prepare the constructor arguments
        $args = array(
            $mockRegistryProcessor,
            $mockGenerator,
            $mockLoggers
        );

        // create a mock configuration
        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\ConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\ConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->any())
                          ->method('getOperationName')
                          ->willReturn('add-update');

        // create a mock subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getConfiguration')
                                 ->willReturn($mockConfiguration);
        $mockSubjectConfiguration->expects($this->any())
                                 ->method('getCallbacks')
                                 ->willReturn(array());

        // initialize the abstract subject that has to be tested
        $this->abstractSubject = $this->getMockBuilder('TechDivision\Import\Subjects\AbstractSubject')
                                      ->setConstructorArgs($args)
                                      ->setMethods(array('isFile', 'touch', 'rename', 'write', 'getHeaderMappings'))
                                      ->getMockForAbstractClass();

        // set the mock configuration instance
        $this->abstractSubject->setConfiguration($mockSubjectConfiguration);
        $this->abstractSubject->setUp($this->serial, uniqid());
    }

    /**
     * Test the getSerial() method.
     *
     * @return void
     */
    public function testGetSerial()
    {
        $this->assertSame($this->serial, $this->abstractSubject->getSerial());
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
        $this->assertNull($this->abstractSubject->import($serial = uniqid(), $filename));
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
        $this->abstractSubject->import($serial = uniqid(), $filename);
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
        $this->abstractSubject->import($serial = uniqid(), $filename);
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
        $this->abstractSubject->import($serial = uniqid(), $filename);
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
             ->with(
                 sprintf(
                     'Successfully processed row (operation: add-update) in file %s on line %d',
                     $filename,
                     1
                 )
             )
             ->willReturn(null);

        // create a mock observer
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->once())
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100));

        // register a mock observer
        $this->abstractSubject->registerObserver($mockObserver, $type = 'import');

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
             ->with(
                 sprintf(
                     'Successfully processed row (operation: add-update) in file %s on line %d',
                     $filename,
                     1
                 )
            )
            ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(0))
                     ->method('handle');

        // register the mock observers
        $this->abstractSubject->registerObserver(new MockSkipObserver(), 'import');
        $this->abstractSubject->registerObserver($mockObserver, $type = 'import');

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
        $this->assertSame(1001, $this->abstractSubject->getCoreConfigData('unknown/config/value', $default = 1001));
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
        $this->abstractSubject->registerObserver($mockObserver, $type = 'import');

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
        $this->assertNull($this->abstractSubject->getStoreViewCode());
    }

    /**
     * Test the set/getStoreViewCode() method without a default value.
     *
     * @return
     */
    public function testSetGetStoreViewCode()
    {

        // initialize the default value
        $default = 'test';

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
             ->with(
                 sprintf(
                         'Successfully processed row (operation: add-update) in file %s on line %d',
                         $filename,
                         1
                     )
                 )
                 ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(1))
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100, 2 => $storeViewCode = 'en_US'));

        // register the mock observers
        $this->abstractSubject->registerObserver($mockObserver, $type = 'import');

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
             ->with(
                 sprintf(
                     'Successfully processed row (operation: add-update) in file %s on line %d',
                     $filename,
                     1
                 )
             )
             ->willReturn(null);

        // create a mock observer and make sure, that it's handle() method will NOT be invoked
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();
        $mockObserver->expects($this->exactly(1))
                     ->method('handle')
                     ->willReturn($row = array(0 => 'value1', 1 => 100, 2 => $storeViewCode = 'en_US'));

        // register the mock observers
        $this->abstractSubject->registerObserver($mockObserver, $type = 'import');

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
             ->with(
                 sprintf(
                     'Successfully processed row (operation: add-update) in file %s on line %d',
                     $filename,
                     1
                 )
             )
             ->willReturn(null);

        // register the mock observers
        $this->abstractSubject->registerObserver(new MockPrepareStoreViewCodeObserver(), $type = 'import');

        // start importing the row
        $this->abstractSubject->importRow(array(0 => 'value1', 1 => 100, 2 => $storeViewCode = 'en_US'));

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
}
