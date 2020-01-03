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
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeUnserializeWithSuccess()
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
