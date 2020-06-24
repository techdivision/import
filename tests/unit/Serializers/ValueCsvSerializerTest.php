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
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // create and initialize the CSV value serializer
        $this->valueCsvSerializer = new ValueCsvSerializer();
        $this->valueCsvSerializer->init($this->getMockCsvConfiguration());
    }

    /**
     * Tests the serialization of the values in the column `attribute_option_values` which contains a
     * list with the attributes available option values and has to serialized/unserialized two times.
     *
     * @return void
     * @attributes
     */
    public function testSerializeAttributeOptionValuesWithSuccess()
    {

        // initialize the array containing the unserialized value
        $unserialized = array('Ventilsicherung von 2" bis 8", nur in geschlossener Position');

        // initialize the serialization result
        $expectedResult = '"""Ventilsicherung von 2"""" bis 8"""", nur in geschlossener Position"""';

        // serialize the value two times
        $serialized = $this->valueCsvSerializer->serialize(array($this->valueCsvSerializer->serialize($unserialized)));

        // assert that the result matchtes the expected result
        $this->assertEquals($expectedResult, $serialized);

        // unserialize the serialized value and query whether or not we've the source value
        $values = current($this->valueCsvSerializer->unserialize($serialized));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($values));
    }

    /**
     * Tests the serialization of the values in the column `attribute_option_values` which contains a
     * list with the attributes available option values and has to serialized/unserialized two times.
     *
     * @return void
     * @attributes
     */
    public function testSerializeAttributeOptionValuesWithExampleValues()
    {

        // initialize the array containing the unserialized value
        $unserialized = array(
            'bla',
            'bla "blub"',
            'bla, blub',
            'bla, "blub" bla',
            'bla "blub, bla"'
        );

        // initialize the serialization result
        $firstResult = 'bla,"bla ""blub""","bla, blub","bla, ""blub"" bla","bla ""blub, bla"""';
        $secondResult = '"bla,""bla """"blub"""""",""bla, blub"",""bla, """"blub"""" bla"",""bla """"blub, bla"""""""';

        // serialize and assert that the result matchtes the expected result two times
        $this->assertEquals($firstResult, $first = $this->valueCsvSerializer->serialize($unserialized));
        $this->assertEquals($secondResult, $serialized = $this->valueCsvSerializer->serialize(array($first)));

        // unserialize the serialized value and query whether or not we've the source value
        $values = current($this->valueCsvSerializer->unserialize($serialized));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($values));
    }

    /**
     * Tests the unserialization of the values in the column `attribute_option_values` which contains a
     * list with the attributes available option values and has to unserialized two times.
     *
     * @return void
     * @attributes
     */
    public function testUnserializeAttributeOptionValuesWithExampleValues()
    {

        // initialize the array containing the unserialized value
        $unserialized = array(
            'bla',
            'bla "blub"',
            'bla, blub',
            'bla, "blub" bla',
            'bla "blub, bla"'
        );

        // initialize the serialization result
        // optional: '"""bla"",""bla """"blub"""""",""bla, blub"",""bla, """"blub"""" bla"",""bla """"blub, bla"""""""';
        $secondResult = '"bla,""bla """"blub"""""",""bla, blub"",""bla, """"blub"""" bla"",""bla """"blub, bla"""""""';

        // unserialize the serialized value and query whether or not we've the source value
        $values = current($this->valueCsvSerializer->unserialize($secondResult));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($values));
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     * @attributes
     */
    public function testSerializeAttributeOptionValuesWithSingleQuotes()
    {

        // initialize the array containing the unserialized value
        $unserialized = array(
            'Aushang-Set "Allgemeine Betriebsanweisungen für Tätigkeiten mit Gefahrstoffen"',
            'Aushang "ADR-Transportplaner"',
            'Damit nicht nur Ihre Räume, sondern auch Ihre Reinigungspläne "sauber" sind.'
        );

        // initialize the expected result
        $expectedResult = '"""Aushang-Set """"Allgemeine Betriebsanweisungen für Tätigkeiten mit Gefahrstoffen"""""",""Aushang """"ADR-Transportplaner"""""",""Damit nicht nur Ihre Räume, sondern auch Ihre Reinigungspläne """"sauber"""" sind."""';

        // serialize the value two times
        $serialized = $this->valueCsvSerializer->serialize(array($this->valueCsvSerializer->serialize($unserialized)));

        // assert that the result matchtes the expected result
        $this->assertEquals($expectedResult, $serialized);

        // unserialize the serialized value and query whether or not we've the source value
        $values = current($this->valueCsvSerializer->unserialize($serialized));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($values));
    }

    /**
     * Test (un-)serialization process of a value for the column `configurable_variations` without quotes.
     *
     * @return void
     * @products
     */
    public function testSerializeConfigurableVariations()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            'sku=configurable-test-Black-55 cm,color=Black,size=55 cm',
            'sku=configurable-test-Black-XS,color=Black,size=XS',
            'sku=configurable-test-Blue-XS,color=Blue,size=XS',
            'sku=configurable-test-Blue-55 cm,color=Blue,size=55 cm'
        );

        // initialize the expected serialization result
        $expectedResult = '"sku=configurable-test-Black-55 cm,color=Black,size=55 cm"|sku=configurable-test-Black-XS,color=Black,size=XS|sku=configurable-test-Blue-XS,color=Blue,size=XS|"sku=configurable-test-Blue-55 cm,color=Blue,size=55 cm"';

        // assert that the (un-)serialization contains the source values/the expected result
        $this->assertEquals($expectedResult, $serialized = $this->valueCsvSerializer->serialize($unserialized, '|'));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($serialized, '|'));
    }

    /**
     * Test (un-)serialization process of a value for the column `configurable_variations` with single quotes.
     *
     * @return void
     * @products
     */
    public function testSerializeConfigurableVariationsWithOneValueAndQuotes()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            'sku=12345,dmeu_lockingmechanism="Anschlussgewinde: 3/4"'
        );

        // initialize the expected serialization result
        $expectedResult = '"sku=12345,dmeu_lockingmechanism=""Anschlussgewinde: 3/4"""';

        // assert that the (un-)serialization contains the source values/the expected result
        $this->assertEquals($expectedResult, $serialized = $this->valueCsvSerializer->serialize($unserialized, '|'));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($serialized, '|'));
    }

    /**
     * Test the (un-)serialization for values in the column `configurable_variations` for
     * the product import which contains quotes and has manually been downgraded.
     *
     * @return void
     * @products
     */
    public function testSerializeMultipleConfigurableVariationsWithQuotes()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            array(
                'sku=12345',
                'dmeu_lockingmechanism="Anschlussgewinde: 3/4"'
            ),
            array(
                'sku=12346',
                'dmeu_lockingmechanism="Anschlussgewinde: 2/4"'
            )
        );

        // initialize the expected serialization result
        $expectedResult = '"sku=12345,""dmeu_lockingmechanism=""""Anschlussgewinde: 3/4"""""""|"sku=12346,""dmeu_lockingmechanism=""""Anschlussgewinde: 2/4"""""""';

        // serialize the values
        $vals = array();
        foreach ($unserialized as $configurable) {
            $vals[] = $this->valueCsvSerializer->serialize($configurable);
        }

        // assert that the serialization contains the expected result
        $this->assertEquals($expectedResult, $serialized = $this->valueCsvSerializer->serialize($vals, '|'));

        // unserialize the serialized value
        $attributes = array();
        foreach ($this->valueCsvSerializer->unserialize($serialized, '|') as $configurable) {
            $attributes[] = $this->valueCsvSerializer->unserialize($configurable);
        }

        // assert that the unserialization contains the source values
        $this->assertEquals($unserialized, $attributes);
    }

    /**
     * Test the unserialization for values in the column `configurable_variations` for
     * the product import which contains quotes and has manually been downgraded.
     *
     * @return
     * @products
     */
    public function testUnserializeMultipleConfigurableVariationsWithQuotesManuallyDowngraded()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            array(
                'sku=12345',
                'dmeu_lockingmechanism="Anschlussgewinde: 3/4"'
            ),
            array(
                'sku=12346',
                'dmeu_lockingmechanism="Anschlussgewinde: 2/4"'
            )
        );

        // initialize the expected serialization result
        $expectedResult = '"sku=12345,dmeu_lockingmechanism=""Anschlussgewinde: 3/4"""|"sku=12346,dmeu_lockingmechanism=""Anschlussgewinde: 2/4"""';

        // unserializa the serialized value
        $vals = array();
        foreach ($this->valueCsvSerializer->unserialize($expectedResult, '|') as $configurable) {
            $vals[] = $this->valueCsvSerializer->unserialize($configurable);
        }

        // assert that the unserialization contains the source values
        $this->assertEquals($unserialized, $vals);
    }

    /**
     * Test the serialization for values in a EAV attribute column of type `multiselect`
     * for the product import which contains quotes.
     *
     * @return
     * @products
     */
    public function testSerializeMultiselectValuesWithQuotes()
    {


        // initialize the array with the values that have to be serialized
        $unserialized = array(
            '"lorem", ipsum "dolor" somit',
            'Sic transit gloria mundi'
        );

        // initialize the expected serialization result
        $expectedResult = '"""""""lorem"""", ipsum """"dolor"""" somit""|""Sic transit gloria mundi"""';

        // assert that the serialization has the expected result
        $this->assertEquals($expectedResult, $this->valueCsvSerializer->serialize(array($this->valueCsvSerializer->serialize($unserialized, '|'))));

        // unserializa the serialized value
        $vals = array();
        foreach ($this->valueCsvSerializer->unserialize($expectedResult) as $v) {
            $vals = array_merge($vals, $this->valueCsvSerializer->unserialize($v, '|'));
        }

        // assert that the unserialization contains the source values
        $this->assertEquals($unserialized, $vals);
    }

    /**
     * Test the serialization for values in a EAV attribute column of type `multiselect`
     * for the product import which contains quotes and a pipe.
     *
     * @return
     * @products
     */
    public function testSerializeMultiselectValuesWithSingleQuotesAndPipe()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            '"lorem", ipsum "dolor" somit | Sic transit gloria mundi'
        );

        // initialize the expected serialization result
        $expectedResult = '"""""""lorem"""", ipsum """"dolor"""" somit | Sic transit gloria mundi"""';

        // assert that the serialization has the expected result
        $this->assertEquals($expectedResult, $this->valueCsvSerializer->serialize(array($this->valueCsvSerializer->serialize($unserialized, '|'))));

        // unserializa the serialized value
        $vals = array();
        foreach ($this->valueCsvSerializer->unserialize($expectedResult) as $v) {
            $vals = array_merge($vals, $this->valueCsvSerializer->unserialize($v, '|'));
        }

        // assert that the unserialization contains the source values
        $this->assertEquals($unserialized, $vals);
    }

    /**
     * Test the serialization for values in a EAV attribute column of type `multiselect`
     * for the product import which contains quotes and pipes.
     *
     * @return
     * @products
     */
    public function testSerializeMultiselectValuesAndQuotesAndPipes()
    {

        // initialize the array with the values that have to be serialized
        $unserialized = array(
            '"lorem", ipsum "dolor" somit | Sic transit gloria mundi 1',
            '"lorem", ipsum "dolor" somit | Sic transit gloria mundi 2'
        );

        // initialize the expected serialization result
        $expectedResult = '"""""""lorem"""", ipsum """"dolor"""" somit | Sic transit gloria mundi 1""|""""""lorem"""", ipsum """"dolor"""" somit | Sic transit gloria mundi 2"""';

        // assert that the serialization has the expected result
        $this->assertEquals($expectedResult, $this->valueCsvSerializer->serialize(array($this->valueCsvSerializer->serialize($unserialized, '|'))));

        // unserializa the serialized value
        $vals = array();
        foreach ($this->valueCsvSerializer->unserialize($expectedResult) as $v) {
            $vals = array_merge($vals, $this->valueCsvSerializer->unserialize($v, '|'));
        }

        // assert that the unserialization contains the source values
        $this->assertEquals($unserialized, $vals);
    }

    /**
     * Test the unserialization for values in a EAV attribute column for the product import
     * which contains a single value, e. g. description.
     *
     * @return void
     * @products
     */
    public function testUnserializeProductAttributeOptionValueWithSuccess()
    {

        // initialize the value we want to unserialized
        $serialized = '"Ventilsicherung von 2"" bis 8"", nur in geschlossener Position"';

        // initialize the expected result
        $expectedResult = array('Ventilsicherung von 2" bis 8", nur in geschlossener Position');

        // unserialize the value and assert the expected result has been returned
        $this->assertEquals($expectedResult, $this->valueCsvSerializer->unserialize($serialized));
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeAttributeOptionsWithQuotesAndSuccess()
    {
        $this->assertEquals('"ac_\01",ov_01', $serialized = $this->valueCsvSerializer->serialize($unserialized = array('ac_\\01','ov_01')));
        $this->assertEquals($unserialized, $this->valueCsvSerializer->unserialize($serialized));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeSerializeWithSuccess()
    {
        $this->assertEquals(array('ac_,01','ov_01'), $unserialized = $this->valueCsvSerializer->unserialize($serialized = '"ac_,01",ov_01'));
        $this->assertEquals($serialized, $this->valueCsvSerializer->serialize($unserialized));
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
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesWithQuotes()
    {
        $this->assertEquals(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin Geprüft"'),
            $this->valueCsvSerializer->unserialize('Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin Geprüft"', '/')
         );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesWithQuotesAroundSlash()
    {
        $this->assertEquals(
            array('Default Category', '"Meine/Euere"', 'Produkte'),
            $this->valueCsvSerializer->unserialize('Default Category/"""Meine/Euere"""/Produkte', '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesWithQuotesAndSlash()
    {
        $this->assertEquals(
            array('Default Category', 'Meine', '"Unsere"', 'Produkte'),
            $this->valueCsvSerializer->unserialize('Default Category/Meine/"""Unsere"""/Produkte', '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains a slash.
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
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains a slash in a middle element.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithSlashInMiddleElement()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::PATH) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/""Deine/Meine""/Produkte/Subkategorie"');

        $this->assertEquals(
            array('Default Category', 'Deine/Meine', 'Produkte', 'Subkategorie'),
            $this->valueCsvSerializer->unserialize(array_shift($column), '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a column with categories that contains a slash within qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithSlashWithinQuotes()
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
     * Tests if the unserialize() method returns the serialized value from a column with categories that contains a slash within qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithSlashAndQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::PATH) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Etiketten und Prüfplaketten/Prüfplaketten/""Prüfplaketten / """"Nächster Prüftermin Geprüft"""""""');

        // explode the columns
        $this->assertEquals(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten / "Nächster Prüftermin Geprüft"'),
            $this->valueCsvSerializer->unserialize(array_shift($column), '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a column with categories that contains qoutes.
     *
     * @return void
     */
    public function testUnserializeCategoriesFromAColumnWithQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::PATH) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten ""Nächster Prüftermin Geprüft"""');

        // explode the columns
        $this->assertEquals(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin Geprüft"'),
            $this->valueCsvSerializer->unserialize(array_shift($column), '/')
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories that contains double qoutes and a slash.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithSinglePath()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör"');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains a single path which contains a comma.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithSinglePathAndComma()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc."""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains a single path which contains quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithSinglePathAndQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten ""Nächster Prüftermin / Geprüft"""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin / Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains a single path which contains a comma within quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithSinglePathAndCommaInQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin , Geprüft"""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin , Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains a single path which contains a comma outside quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithSinglePathAndCommaOutOfQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test """"Nächster Prüftermin / Geprüft"""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test "Nächster Prüftermin / Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPath()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör,Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör 2"');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör',
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör 2'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and both of them contains commas.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaInBoth()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc."",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc. 2"""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc. 2'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and both of them contains quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndQuotesInBoth()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin / Geprüft"""""",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin / Geprüft"""" 2"""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin / Geprüft"',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin / Geprüft" 2'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and both of them contains commas within quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaInBothQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin , Geprüft"""""",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin , Geprüft"""" 2"""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin , Geprüft"',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin , Geprüft" 2'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and both of them contains commas outside quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaOutOfBothQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test """"Nächster Prüftermin / Geprüft"""""",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test """"Nächster Prüftermin / Geprüft"""" 2"""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test "Nächster Prüftermin / Geprüft"',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test "Nächster Prüftermin / Geprüft" 2'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and one of them contains commas.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaInOne()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör"",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc."""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfvorschriften - BGV, DGUV etc.'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and one of them contains quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndQuoteInOne()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör"",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin / Geprüft""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin / Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and one of them contains a comma with within quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaInOneQuote()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör"",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten """"Nächster Prüftermin , Geprüft"""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten "Nächster Prüftermin , Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and one of them contains a comma with out of quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndCommaOutOfOneQuote()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör"",""Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test """"Nächster Prüftermin / Geprüft""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Arbeitsschutz und Betriebssicherheit/Brandschutz/Feuerlöscher und Zubehör',
                'Default Category/Etiketten und Prüfplaketten/Prüfplaketten/Prüfplaketten , test "Nächster Prüftermin / Geprüft"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }

    /**
     * Tests if the unserialize() method returns the serialized value from a string with categories
     * that contains multiple paths and quotes with commas inside and within the quotes.
     *
     * @return void
     */
    public function testUnserializeProductCategoryFromAColumnWithMultiPathAndMultipleQuotesWithCommaWithinAndOutOfQuotes()
    {

        // first extract the the column value (simulating what happens when column will be extracted with $this->getValue(ColumnKeys::CATEGORIES) from the CSV file)
        $column = $this->valueCsvSerializer->unserialize('"""Default Category/Level 1 """"test""""/Level 2 """"Lorem, ipsum""""/ Level 3 """"dolor somit"""" and """"sic transit"""""",""Default Category/Another Level 1 """"test""""/Level 2 """"Lorem, ipsum""""/ Level 3 """"dolor somit"""" and """"sic transit"""""""');

        // explode the columns
        $this->assertEquals(
            array(
                'Default Category/Level 1 "test"/Level 2 "Lorem, ipsum"/ Level 3 "dolor somit" and "sic transit"',
                'Default Category/Another Level 1 "test"/Level 2 "Lorem, ipsum"/ Level 3 "dolor somit" and "sic transit"'
            ),
            $this->valueCsvSerializer->unserialize(array_shift($column))
        );
    }
}
