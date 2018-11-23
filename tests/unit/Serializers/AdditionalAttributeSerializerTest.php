<?php

/**
 * TechDivision\Import\Serializers\AdditionalAttributeCsvSerializerTest
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

namespace TechDivision\Import\Serializers;

/**
 * Test class for CSV additional attribute serializer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeCsvSerializerTest extends AbstractSerializerTest
{

    /**
     * The additional attribute serializer we want to test.
     *
     * @var \TechDivision\Import\Serializers\AdditionalAttributeCsvSerializer
     */
    protected $additionalAttributeSerializer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        $valueCsvSerializerFactoryMock = $this->getMockBuilder(ConfigurationAwareSerializerFactoryInterface::class)->getMock();

        $valueCsvSerializer = new ValueCsvSerializer();
        $valueCsvSerializer->init($mockConfiguration = $this->getMockConfiguration());

        $valueCsvSerializerFactoryMock->expects($this->any())->method('createSerializer')->willReturn($valueCsvSerializer);

        $this->additionalAttributeSerializer = new AdditionalAttributeCsvSerializer($valueCsvSerializerFactoryMock);
        $this->additionalAttributeSerializer->init($mockConfiguration);
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeEmptyArrayWithSuccess()
    {

        // initialize the CSV value serializer
        $this->additionalAttributeSerializer->init($this->getMockConfiguration());

        // serialize the array and test the result
        $this->assertEquals(null, $this->additionalAttributeSerializer->serialize(array()));
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeWithSuccess()
    {

        // initialize the CSV value serializer
        $this->additionalAttributeSerializer->init($this->getMockConfiguration());

        // serialize the array and test the result
        $this->assertEquals('"ac_01=ov_01","ac_02=ov_02"', $this->additionalAttributeSerializer->serialize(array('ac_01' => 'ov_01', 'ac_02' => 'ov_02')));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeEmptyArrayWithSuccess()
    {

        // initialize the CSV value serializer
        $this->additionalAttributeSerializer->init($this->getMockConfiguration());

        // unserialize the values and test the result
        $this->assertEquals(array(), $this->additionalAttributeSerializer->unserialize(null));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeWithSuccess()
    {

        // initialize the CSV value serializer
        $this->additionalAttributeSerializer->init($this->getMockConfiguration());

        // unserialize the values and test the result
        $this->assertEquals(array('ac_01' => 'ov_01', 'ac_02' => 'ov_02'), $this->additionalAttributeSerializer->unserialize('"ac_01=ov_01","ac_02=ov_02"'));
    }

    /**
     * Tests if the unserialize() method returns the serialized value, if only one value is available.
     *
     * @return void
     */
    public function testUnserializeSingleAdditionalAttribute()
    {

            // initialize the CSV value serializer
        $this->additionalAttributeSerializer->init($this->getMockConfiguration());

        // unserialize the values and test the result
        $this->assertEquals(array('delivery_date_1' => '2019011'), $this->additionalAttributeSerializer->unserialize('"delivery_date_1=2019011"'));
    }
}
