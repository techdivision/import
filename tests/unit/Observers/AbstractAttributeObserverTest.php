<?php

/**
 * TechDivision\Import\Observers\AbstractAttributeObserverTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Observers;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\ConfigurationKeys;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractAttributeObserverTest extends TestCase
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
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->attributeObserver = $this->getMockBuilder('TechDivision\Import\Observers\AbstractAttributeObserver')
                                        ->setMethods(array('isDebugMode', 'getPrimaryKeyMemberName', 'getPrimaryKey'))
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

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // prepare the headers
        $headers = array($attributeCode = 'unknown_attribute_code' => 0);

        // prepare the row
        $row = array(0 => 'a-attribute-value');

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
        );

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(sprintf('Can\'t find attribute with attribute code "%s"', $attributeCode))
                         ->willReturn(null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();

        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('appendExceptionSuffix')
                    ->willReturnArgument(0);
        $mockSubject->expects($this->once())
                    ->method('getSystemLogger')
                    ->willReturn($mockSystemLogger);
        $mockSubject->expects($this->once())
                    ->method('appendExceptionSuffix')
                    ->willReturnArgument(0);
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
        $mockSubject->expects($this->once())
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // prepare the headers
        $headers = array($attributeCode = 'attribute_code' => 0);

        // prepare the row
        $row = array(0 => 'a-attribute-value');

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
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
                         ->with(sprintf('Found attribute with attribute code "%s"', $attributeCode))
                         ->willReturn(null);
        $mockSystemLogger->expects($this->once())
                        ->method('warning')
                        ->with(sprintf('Found EMTPY backend type for attribute "%s"', $attributeCode))
                        ->willReturn(null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->exactly(2))
                    ->method('appendExceptionSuffix')
                    ->willReturnArgument(0);
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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
                                ->method('getPrimaryKeyMemberName')
                                ->willReturn(MemberNames::ENTITY_ID);
        $this->attributeObserver->expects($this->exactly(2))
                                ->method('getPrimaryKey')
                                ->willReturn($lastEntityId = 100);

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
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
        $headers = array($attributeCode => 0);

        // prepare the row
        $row = array(0 => $attributeValue = 'test-url-key');

        // prepare the varchar attribute that has to be persisted
        $varcharAttribute = array(
            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE,
            MemberNames::ENTITY_ID    => $lastEntityId,
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
                         ->with(sprintf('Found attribute with attribute code "%s"', $attributeCode))
                         ->willReturn(null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('appendExceptionSuffix')
                    ->willReturnArgument(0);
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
                    ->method('castValueByBackendType')
                    ->with($backendType, $attributeValue)
                    ->willReturn($attributeValue);
        $mockSubject->expects($this->exactly(2))
                    ->method('getRowStoreId')
                    ->with(StoreViewCodes::ADMIN)
                    ->willReturn($storeId);
        $mockSubject->expects($this->once())
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
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
        $headers = array($attributeCode => 0);

        // prepare the row
        $row = array(0 => null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');

        $mockJmsConfiguration->expects($this->once())
            ->method('getEmptyAttributeValueConstant')
            ->willReturn('FooBar');

        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
        );

        // prepare the attributes
        $attributes = array(
            $attributeCode = 'url_key' => array(
                MemberNames::ATTRIBUTE_ID => 124,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => $attributeCode,
                MemberNames::BACKEND_TYPE => BackendTypeKeys::BACKEND_TYPE_VARCHAR,
                'is_required' => 0
            )
        );

        // prepare the headers
        $headers = array($attributeCode => 0);

        // prepare the row
        $row = array(0 => 'test-url-key');

        // mock the callbacks
        $callback = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackInterface')
                         ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackInterface'))
                         ->getMock();
        $callback->expects($this->once())
                 ->method('handle')
                 ->willReturn(null);
        $callbacks = array($callback);

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(sprintf('Skipped processing attribute "%s"', $attributeCode))
                         ->willReturn(null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                    ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                    ->getMock();
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
                    ->willReturn($callbacks);
        $mockSubject->expects($this->once())
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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

        // prepare the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
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
        $headers = array($attributeCode => 0);

        // prepare the row
        $row = array(0 => 'test-url-key');

        // mock a system logger
        $mockSystemLogger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                                 ->setMethods(get_class_methods('Psr\Log\LoggerInterface'))
                                 ->getMock();
        $mockSystemLogger->expects($this->once())
                         ->method('debug')
                         ->with(sprintf('Found invalid backend type %s for attribute "%s"', $backendType, $attributeCode))
                         ->willReturn(null);

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('appendExceptionSuffix')
                    ->willReturnArgument(0);
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
        $mockSubject->expects($this->once())
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

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
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
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
        $headers = array($attributeCode => 0);

        // prepare the row
        $row = array(0 => 'test-sku');

        // mock a subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                         ->getMock();
        $mockSubjectConfiguration->expects($this->once())
                                 ->method('hasParam')
                                 ->with(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)
                                 ->willReturn(false);

        // mock a jms configuration
        $mockJmsConfiguration = $this->createMock('TechDivision\Import\Configuration\ConfigurationInterface');
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockJmsConfiguration);

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->atLeastOnce())
                    ->method('getConfiguration')
                    ->willReturn($mockSubjectConfiguration);
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
                    ->method('getDefaultColumnValues')
                    ->willReturn(array());

        // make sure the persist method will NEVER be invoked
        $this->attributeObserver->expects($this->never())
                                ->method('persistVarcharAttribute');

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }
}
