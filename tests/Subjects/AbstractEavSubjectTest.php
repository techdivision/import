<?php

/**
 * TechDivision\Import\Subjects\AbstractEavSubjectTest
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

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Utils\FrontendInputTypes;

/**
 * Test class for the abstract EAV subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractEavSubjectTest extends AbstractTest
{

    /**
     * The abstract EAV subject that has to be tested.
     *
     * @var \TechDivision\Import\Subjects\AbstractEavSubject
     */
    protected $abstractEavSubject;

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
        return 'TechDivision\Import\Subjects\AbstractEavSubject';
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
     * Mock the global data.
     *
     * @return array The array with the global data
     */
    protected function getMockGlobalData()
    {

        // initialize the global data
        $globalData = array(
            RegistryKeys::GLOBAL_DATA => array(
                RegistryKeys::EAV_ATTRIBUTES => array(
                    EntityTypeCodes::CATALOG_PRODUCT => array(
                        'Default' => array(
                            'default_attribute' => array(
                                MemberNames::ATTRIBUTE_ID => 1,
                                MemberNames::ENTITY_TYPE_ID => 4,
                                MemberNames::ATTRIBUTE_CODE => 'default_attribute'
                            )
                        )
                    )
                ),
                RegistryKeys::EAV_USER_DEFINED_ATTRIBUTES => array(
                    EntityTypeCodes::CATALOG_PRODUCT => array(
                        array(
                            MemberNames::ATTRIBUTE_ID => 2,
                            MemberNames::ENTITY_TYPE_ID => 4,
                            MemberNames::ATTRIBUTE_CODE => 'custom_attribute',
                            MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                        )
                    )
                ),
                RegistryKeys::ATTRIBUTE_SETS => array(
                    EntityTypeCodes::CATALOG_PRODUCT => array(
                        'Default' => array(
                            MemberNames::ATTRIBUTE_SET_ID => 4,
                            MemberNames::ENTITY_TYPE_ID => 4,
                            MemberNames::ATTRIBUTE_SET_NAME => 'Default'
                        )
                    )
                )
            )
        );

        // merge the global data with the parent one
        return array_merge_recursive(parent::getMockGlobalData(), $globalData);
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
        $this->abstractEavSubject = $this->getSubjectInstance();
        $this->abstractEavSubject->setUp($this->serial = uniqid());
    }

    /**
     * Test the get/setAttributeSet() method.
     *
     * @return void
     */
    public function testSetGetAttributeSet()
    {

        // initialize a attribute set
        $attributeSet = array(MemberNames::ATTRIBUTE_SET_NAME => 'Default');

        // set/get the attribute set
        $this->abstractEavSubject->setAttributeSet($attributeSet);
        $this->assertSame($attributeSet, $this->abstractEavSubject->getAttributeSet());
    }

    /**
     * Test the getAttributes() method.
     *
     * @return void
     */
    public function testGetAttributes()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // set the attribute set name
        $this->abstractEavSubject->setAttributeSet(array(MemberNames::ATTRIBUTE_SET_NAME => 'Default'));

        // laod and count the attributes
        $this->assertCount(1, $attributes = $this->abstractEavSubject->getAttributes());
        $this->assertSame(
            array(
                MemberNames::ATTRIBUTE_ID => 1,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => 'default_attribute'
            ),
            $attributes['default_attribute']
        );
    }

    /**
     * Test the getAttributes() method with invalid entity type.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Found invalid entity type code "unknown_entity_type"
     */
    public function testGetAttributesWithInvalidEntityTypeCode()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn('unknown_entity_type');

        // try to load the attributes
        $this->abstractEavSubject->getAttributes();
    }

    /**
     * Test the getAttributes() method with invalid attribute set name.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Found invalid attribute set name "Unknown"
     */
    public function testGetAttributesWithInvalidAttributeSetName()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

         // set the attribute set name
         $this->abstractEavSubject->setAttributeSet(array(MemberNames::ATTRIBUTE_SET_NAME => 'Unknown'));

        // try to load the attributes
        $this->abstractEavSubject->getAttributes();
    }

    /**
     * Test the getAttributeSetByAttributeSetName() method.
     *
     * @return void
     */
    public function testGetAttributSetByAttributeSetName()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // initialize the exptected attribute set
        $attributeSet = array(
            MemberNames::ATTRIBUTE_SET_ID => 4,
            MemberNames::ENTITY_TYPE_ID => 4,
            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetName = 'Default'
        );

        // laod and count the attributes
        $this->assertSame(
            $attributeSet,
            $this->abstractEavSubject->getAttributeSetByAttributeSetName($attributeSetName)
        );
    }

    /**
     * Test the getAttributeSetByAttributeSetName() method with an invalid attribute set name.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Found invalid entity type code "unknown_entity_type"
     */
    public function testGetAttributSetByAttributSetNameWithInvalidEntityTypeCode()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn('unknown_entity_type');

        // laod and count the attributes
        $this->abstractEavSubject->getAttributeSetByAttributeSetName('Default');
    }

    /**
     * Test the getAttributeSetByAttributeSetName() method with an invalid attribute set name.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Found invalid attribute set name "Unknown"
     */
    public function testGetAttributSetByAttributSetNameWithInvalidAttributeSetName()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // laod and count the attributes
        $this->abstractEavSubject->getAttributeSetByAttributeSetName('Unknown');
    }

    /**
     * Test the getBackendTypes() method.
     *
     * @return void
     */
    public function testGetBackendTypes()
    {

        // initialize the backend types
        $backendTypes = array(
            BackendTypeKeys::BACKEND_TYPE_DATETIME => array('persistDatetimeAttribute', 'loadDatetimeAttribute', 'deleteDatetimeAttribute'),
            BackendTypeKeys::BACKEND_TYPE_DECIMAL  => array('persistDecimalAttribute', 'loadDecimalAttribute', 'deleteDecimalAttribute'),
            BackendTypeKeys::BACKEND_TYPE_INT      => array('persistIntAttribute', 'loadIntAttribute', 'deleteIntAttribute'),
            BackendTypeKeys::BACKEND_TYPE_TEXT     => array('persistTextAttribute', 'loadTextAttribute', 'deleteTextAttribute'),
            BackendTypeKeys::BACKEND_TYPE_VARCHAR  => array('persistVarcharAttribute', 'loadVarcharAttribute', 'deleteVarcharAttribute')
        );

        // query whether or not the backend types are returned
        $this->assertSame($backendTypes, $this->abstractEavSubject->getBackendTypes());
    }

    /**
     * Data provider with several values to be casted to their given backend type.
     *
     * @return array The backen type, value => result array
     */
    public function backendTypeProvider()
    {
        return array(
            array(BackendTypeKeys::BACKEND_TYPE_DATETIME, '10/21/16, 9:10 AM', '2016-10-21 09:10:00'),
            array(BackendTypeKeys::BACKEND_TYPE_DATETIME, '10/21/16, 9:10 PM', '2016-10-21 21:10:00'),
            array(BackendTypeKeys::BACKEND_TYPE_FLOAT, '101.00', 101.00),
            array(BackendTypeKeys::BACKEND_TYPE_FLOAT, '0.99', 0.99),
            array(BackendTypeKeys::BACKEND_TYPE_INT, '101', 101),
            array(BackendTypeKeys::BACKEND_TYPE_INT, '1', 1),
            array(BackendTypeKeys::BACKEND_TYPE_INT, '0', 0),
            array(BackendTypeKeys::BACKEND_TYPE_VARCHAR, 'test', 'test'),
            array(BackendTypeKeys::BACKEND_TYPE_VARCHAR, NULL, NULL)
        );
    }

    /**
     * Test the castValueByBackendType() method.
     *
     * @param string      $backendType The backend type we want to cast to
     * @param string|null $value       The value that should be cased
     * @param mixed       $expected    The expected casting result
     *
     * @return void
     *
     * @dataProvider backendTypeProvider()
     */
    public function testCastValueByBackendType($backendType, $value, $expected)
    {

        // set the configuration value for the source date format
        $this->abstractEavSubject
             ->getConfiguration()
             ->expects($this->any())
             ->method('getSourceDateFormat')
             ->willReturn('n/d/y, g:i A');

        // cast the passed values
        $this->assertSame($expected, $this->abstractEavSubject->castValueByBackendType($backendType, $value));
    }

    /**
     * Test the getEavUserDefinedAttributes() method.
     *
     * @return void
     */
    public function testGetEavUserDefinedAttributes()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // initialize the array with the expected EAV user defined attributes
        $userDefinedAttributes = array(
            array(
                MemberNames::ATTRIBUTE_ID => 2,
                MemberNames::ENTITY_TYPE_ID => 4,
                MemberNames::ATTRIBUTE_CODE => 'custom_attribute',
                MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
            )
        );

        // query that the EAV user defined attributes are returned
        $this->assertSame($userDefinedAttributes, $this->abstractEavSubject->getEavUserDefinedAttributes());
    }

    /**
     * Test the getEavAttributeByAttributeCode() method.
     *
     * @return void
     */
    public function testGetEavAttributeByAttributeCode()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // set the attribute set name
        $this->abstractEavSubject->setAttributeSet(array(MemberNames::ATTRIBUTE_SET_NAME => 'Default'));

        // initialize the expected attribute
        $attribute = array(
            MemberNames::ATTRIBUTE_ID => 1,
            MemberNames::ENTITY_TYPE_ID => 4,
            MemberNames::ATTRIBUTE_CODE => 'default_attribute'
        );

        // laod and count the attributes
        $this->assertSame($attribute, $this->abstractEavSubject->getEavAttributeByAttributeCode('default_attribute'));
    }

    /**
     * Test the getEavAttributeByAttributeCode() method.
     *
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Can't load attribute with code "unknown_attribute_code"
     */
    public function testGetEavAttributeByAttributeCodeWithInvalidAttributeCode()
    {

        // mock the entity type code
        $this->abstractEavSubject
             ->getConfiguration()
             ->getConfiguration()
             ->expects($this->once())
             ->method('getEntityTypeCode')
             ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // set the attribute set name
        $this->abstractEavSubject->setAttributeSet(array(MemberNames::ATTRIBUTE_SET_NAME => 'Default'));

        // try to load the attribute
        $this->abstractEavSubject->getEavAttributeByAttributeCode('unknown_attribute_code');
    }

    /**
     * Test the getDefaultCallbackMappings() method when frontend input callback mappings have been merged.
     *
     * @return void
     */
    public function testGetCallbackMappingsWithMergeFrontendInputCallbackMappings()
    {

        // create the subject instance we want to test and invoke the setup method
        $abstractEavSubject = $this->getSubjectInstance(array('getDefaultFrontendInputCallbackMappings'));
        $abstractEavSubject->expects($this->once())
                           ->method('getDefaultFrontendInputCallbackMappings')
                           ->willReturn(
                               array(
                                   FrontendInputTypes::SELECT => 'import_product.callback.select'
                               )
                           );

        // mock the entity type code
        $abstractEavSubject->getConfiguration()
                           ->getConfiguration()
                           ->expects($this->once())
                           ->method('getEntityTypeCode')
                           ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

        // initialize the callback mappings to compare
        $callbackMappings = array(
            'custom_attribute'       => array('import_product.callback.select'),
            'attribute_code'         => array('import.test.callback-01.id'),
            'another_attribute_code' => array('import.test.callback-02.id')
        );

        // invoke the set-up method and qeuery whether or not the callback mappings are set
        $abstractEavSubject->setUp($this->serial = uniqid());
        $this->assertSame($callbackMappings, $abstractEavSubject->getCallbackMappings());
    }

    /**
     * Test getDefaultFrontendInputCallbackMappings() method.
     *
     * @return void
     */
    public function testGetDefaultFrontendInputCallbackMappings()
    {
        $abstractEavSubject = $this->getSubjectInstance();
        $this->assertCount(0, $abstractEavSubject->getDefaultFrontendInputCallbackMappings());
    }
}
