<?php

/**
 * TechDivision\Import\Observers\AbstractAttributeObserverTest
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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractAttributeObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AbstractAttributeObserver
     */
    protected $attributeObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->attributeObserver = $this->getMockBuilder('TechDivision\Import\Observers\AbstractAttributeObserver')
                                        ->setMethods(array('getFilename', 'getLineNumber', 'isDebugMode'))
                                        ->getMockForAbstractClass();
    }

    /**
     * Test the handle() method with an unknown attribute.
     *
     * @return void
     */
    public function testHandleWithUnknownAttribute()
    {

        // mock filename + line number and the method call to the isDebugMode() method
        $this->attributeObserver->expects($this->once())
                                ->method('isDebugMode')
                                ->willReturn(true);
        $this->attributeObserver->expects($this->once())
                                ->method('getFilename')
                                ->willReturn($filename = 'var/importexport/product-import_20171112-102312_01.csv');
        $this->attributeObserver->expects($this->once())
                                ->method('getLineNumber')
                                ->willReturn($lineNumber = 2);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // prepare the headers
        $headers = array(
            $attributeCode = 'unknown_attribute_code' => 0,
        );

        // prepare the row
        $row = array(
            0 => 'a-attribute-value'
        );

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(
                             sprintf(
                                 'Can\'t find attribute with attribute code %s in file %s on line %d',
                                 $attributeCode,
                                 $filename,
                                 $lineNumber
                             )
                         )
                         ->willReturn(null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn(array());
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }



    /**
     * Test the handle() method with an attribute with an empty backend type.
     *
     * @return void
     */
    public function testHandleWithAttributeWithEmptyBackendType()
    {

        // mock filename + line number and the method call to the isDebugMode() method
        $this->attributeObserver->expects($this->once())
                                ->method('isDebugMode')
                                ->willReturn(true);
        $this->attributeObserver->expects($this->exactly(2))
                                ->method('getFilename')
                                ->willReturn($filename = 'var/importexport/product-import_20171112-102312_01.csv');
        $this->attributeObserver->expects($this->exactly(2))
                                ->method('getLineNumber')
                                ->willReturn($lineNumber = 2);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // prepare the headers
        $headers = array(
            $attributeCode = 'attribute_code' => 0,
        );

        // prepare the row
        $row = array(
            0 => 'a-attribute-value'
        );

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes WHITHOUT backend type
        $attributes = array(
            $attributeCode => array(
                MemberNames::ATTRIBUTE_ID   => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE   => null
            )
        );

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(
                             sprintf(
                                 'Found attribute with attribute code %s in file %s on line %d',
                                 $attributeCode,
                                 $filename,
                                 $lineNumber
                             )
                         )
                         ->willReturn(null);
        $mockSystemLogger->expects($this->once())
                        ->method('warning')
                        ->with(
                            sprintf(
                                'Found EMTPY backend type for attribute %s in file %s on line %d',
                                $attributeCode,
                                $filename,
                                $lineNumber
                            )
                        )
                        ->willReturn(null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->exactly(2))
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }

    /**
     * Test the handle() method with an attribute.
     *
     * @return void
     */
    public function testHandleWithAttribute()
    {

        // mock filename + line number and the method call to the isDebugMode() method
        $this->attributeObserver->expects($this->once())
                                ->method('isDebugMode')
                                ->willReturn(true);
        $this->attributeObserver->expects($this->once())
                                ->method('getFilename')
                                ->willReturn($filename = 'var/importexport/product-import_20171112-102312_01.csv');
        $this->attributeObserver->expects($this->once())
                                ->method('getLineNumber')
                                ->willReturn($lineNumber = 2);

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'url_key' => array(
                MemberNames::ATTRIBUTE_ID => $attributeId = 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => $backendType = BackendTypeKeys::BACKEND_TYPE_VARCHAR
            )
        );

        // prepare the headers
        $headers = array(
            $attributeCode => 0,
        );

        // prepare the row
        $row = array(
            0 => $attributeValue = 'test-url-key'
        );


        // prepare the varchar attribute that has to be persisted
        $varcharAttribute = array(
            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE,
            MemberNames::ENTITY_ID    => $lastEntityId = 100,
            MemberNames::ATTRIBUTE_ID => $attributeId,
            MemberNames::STORE_ID     => $storeId = 1,
            MemberNames::VALUE        => $attributeValue
        );

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(
                             sprintf(
                                 'Found attribute with attribute code %s in file %s on line %d',
                                 $attributeCode,
                                 $filename,
                                 $lineNumber
                             )
                         )
                         ->willReturn(null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);
        $mockSubject->expects($this->once())
                    ->method('getCallbacksByType')
                    ->with($attributeCode)
                    ->willReturn(array());
        $mockSubject->expects($this->once())
                    ->method('getLastEntityId')
                    ->willReturn($lastEntityId);
        $mockSubject->expects($this->once())
                    ->method('castValueByBackendType')
                    ->with($backendType, $attributeValue)
                    ->willReturn($attributeValue);
        $mockSubject->expects($this->once())
                    ->method('getRowStoreId')
                    ->with(StoreViewCodes::ADMIN)
                    ->willReturn($storeId);

        // mock the method call to persist the attribute value
        $this->attributeObserver->expects($this->once())
                                ->method('persistVarcharAttribute')
                                ->with($varcharAttribute)
                                ->willReturn(null);

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);

        // assert that attribute code/value has been initialized
        $this->assertSame($attributeCode, $this->attributeObserver->getAttributeCode());
        $this->assertSame($attributeValue, $this->attributeObserver->getAttributeValue());
    }

    /**
     * Test the handle() method with an empty attribute.
     *
     * @return void
     */
    public function testHandleWithEmptyAttribute()
    {

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'url_key' => array(
                MemberNames::ATTRIBUTE_ID => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => BackendTypeKeys::BACKEND_TYPE_VARCHAR
            )
        );

        // prepare the headers
        $headers = array(
            $attributeCode => 0,
        );

        // prepare the row
        $row = array(
            0 => null
        );

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->never())
                    ->method('getSystemLogger');
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }

    /**
     * Test the handle() method with an attribute and a callback returning an empty attribute.
     *
     * @return void
     */
    public function testHandleWithAttributeAndCallbackReturningEmptyAttributeValue()
    {

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'url_key' => array(
                MemberNames::ATTRIBUTE_ID => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => BackendTypeKeys::BACKEND_TYPE_VARCHAR
            )
        );

        // prepare the headers
        $headers = array(
            $attributeCode => 0
        );

        // prepare the row
        $row = array(
            0 => 'test-url-key'
        );

        // mock the callbacks
        $callback = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackInterface')
                         ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackInterface'))
                         ->getMock();
        $callback->expects($this->once())
                 ->method('handle')
                 ->willReturn(null);
        $callbacks = array($callback);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                    ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                    ->getMock();
        $mockSubject->expects($this->never())
                    ->method('getSystemLogger');
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);
        $mockSubject->expects($this->once())
                    ->method('getCallbacksByType')
                    ->with($attributeCode)
                    ->willReturn($callbacks);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }

    /**
     * Test the handle() method with an attribute with invalid backend type.
     *
     * @return void
     */
    public function testHandleWithAttributeAndInvalidBackendType()
    {

        // mock filename + line number
        $this->attributeObserver->expects($this->once())
                                ->method('getFilename')
                                ->willReturn($filename = 'var/importexport/product-import_20171112-102312_01.csv');
        $this->attributeObserver->expects($this->once())
                                ->method('getLineNumber')
                                ->willReturn($lineNumber = 2);

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'url_key' => array(
                MemberNames::ATTRIBUTE_ID => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => $backendType = 'unknown_backend_type'
            )
        );

        // prepare the headers
        $headers = array(
            $attributeCode => 0,
        );

        // prepare the row
        $row = array(
            0 => 'test-url-key'
        );

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(
                             sprintf(
                                 'Found invalid backend type %s for attribute %s in file %s on line %s',
                                 $backendType,
                                 $attributeCode,
                                 $filename,
                                 $lineNumber
                             )
                         )
                         ->willReturn(null);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }

    /**
     * Test the handle() method with an attribute with static backend type.
     *
     * @return void
     */
    public function testHandleWithAttributeAndStaticdBackendType()
    {

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'sku' => array(
                MemberNames::ATTRIBUTE_ID => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => BackendTypeKeys::BACKEND_TYPE_STATIC
            )
        );

        // prepare the headers
        $headers = array(
            $attributeCode => 0,
        );

        // prepare the row
        $row = array(
            0 => 'test-sku'
        );

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->never())
                    ->method('getSystemLogger');
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn($attributes);
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('getBackendTypes')
                    ->willReturn($backendTypes);

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }
}
