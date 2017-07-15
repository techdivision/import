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

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\EntityStatus;

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
     * @var \TechDivision\Import\Observers\AttributeObserverTraitImpl
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
                                        ->getMockForAbstractClass();
    }

    /**
     * Test the handle() method with a missing attribute.
     *
     * @return void
     */
    public function testHandleWithInvalidAttribute()
    {

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn(array());
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn(array(ColumnKeys::STORE_VIEW_CODE => 0));
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn(array(0 => 'en_US'));

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }

    /**
     * Test the handle() method with a.
     *
     * @return void
     */
    public function testHandleWithAttribute()
    {

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
            ColumnKeys::STORE_VIEW_CODE => 0,
            $attributeCode => 1,
        );

        // prepare the row
        $row = array(
            0 => 'en_US',
            1 => $attributeValue = 'test-url-key'
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
    }
}
