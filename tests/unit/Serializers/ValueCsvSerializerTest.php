<?php

/**
 * TechDivision\Import\Serializers\ValueCsvSerializerTest
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
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ValueCsvSerializerTest extends AbstractSerializerTest
{

    /**
     * The CSV value serializer we want to test.
     *
     * @var \TechDivision\Import\Serializers\ValueCsvSerializer
     */
    protected $valueCsvSerializer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // create and initialize the CSV value serializer
        $this->valueCsvSerializer = new ValueCsvSerializer();
        $this->valueCsvSerializer->init($this->getMockConfiguration());
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeWithSuccess()
    {
        $this->assertEquals('"ac_\\\\01","ov_01"', $this->valueCsvSerializer->serialize(array('ac_\\01','ov_01')));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeWithSuccess()
    {
        $this->assertEquals(array('ac_,01','ov_01'), $this->valueCsvSerializer->unserialize('"ac_,01","ov_01"'));
    }
}
