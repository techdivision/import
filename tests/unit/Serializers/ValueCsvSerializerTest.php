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
        $this->valueCsvSerializer->init($this->getMockCsvConfiguration());
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

    /**
     * Tests if the unserialize() method returns the serialized value for a multiselect attribute which values that contains commas.
     *
     * @return void
     */
    public function testUnserializeMultiselectValueWithValuesContainingCommasWithSuccess()
    {
        $this->assertEquals(array('attr1=ac_,01|ac_,02','attr2=ov_01'), $this->valueCsvSerializer->unserialize('"attr1=ac_,01|ac_,02","attr2=ov_01"'));
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeCategoryNameWithSlashAndDoubleQuotes()
    {
        $this->assertEquals(
            array('Prüfplaketten "Nächster Prüftermin / Geprüft"'),
            $this->valueCsvSerializer->unserialize('"Prüfplaketten ""Nächster Prüftermin / Geprüft"""')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeProductCategoryNameSeparatedWithComma()
    {
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.',
                'Default Category/Sicherheitskennzeichnung und Rettungszeichen/Verbotsschilder/Verbotszeichen neue "ASR A1.3, EN ISO 7010"'
            ),
            $this->valueCsvSerializer->unserialize('"Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.","Default Category/Sicherheitskennzeichnung und Rettungszeichen/Verbotsschilder/Verbotszeichen neue ""ASR A1.3, EN ISO 7010"""')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeCategoriesWithSlashAndDoubleQuotes()
    {
        $this->assertEquals(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft"'),
            $this->valueCsvSerializer->unserialize('Default Category/Etiketten und Prüfplaketten/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"""', '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithSlash()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::PATH) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Sicherheitskennzeichnung und Rettungszeichen/Gefahrstoffkennzeichnung/""Gefahrstoffetiketten gemäß GHS-/CLP"""');

        $this->assertEquals(
            array('Default Category', 'Sicherheitskennzeichnung und Rettungszeichen', 'Gefahrstoffkennzeichnung', 'Gefahrstoffetiketten gemäß GHS-/CLP'),
            $this->valueCsvSerializer->unserialize(array_shift($column), '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a column with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithSlashAndDoubleQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::PATH) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Etiketten und Prüfplaketten/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""""""');

        // explode the columns
        $this->assertEquals(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft"'),
            $this->valueCsvSerializer->unserialize(array_shift($column), '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithComma()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc."",""Default Category/Sicherheitskennzeichnung und Rettungszeichen/Verbotsschilder/Verbotszeichen neue """"ASR A1.3, EN ISO 7010"""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.',
                'Default Category/Sicherheitskennzeichnung und Rettungszeichen/Verbotsschilder/Verbotszeichen neue "ASR A1.3, EN ISO 7010"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }
}
