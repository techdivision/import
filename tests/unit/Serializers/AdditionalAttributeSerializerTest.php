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
* @copyright 2019 TechDivision GmbH <info@techdivision.com>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Serializers;

use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Test class for CSV additional attribute serializer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
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
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // load the default attributes/entity types
        $attributes = $this->getAttributes();
        $entityTypes = $this->getEntityTypes();

        // initialize the mock for the import processor
        $importProcessor = $this->getMockBuilder(ImportProcessorInterface::class)->setMethods(get_class_methods(ImportProcessorInterface::class))->getMock();
        $importProcessor->expects($this->any())->method('getEavEntityTypeByEntityTypeCode')->willReturnCallback(function ($entityTypeCode) use ($entityTypes) {
            return $entityTypes[$entityTypeCode];
        });
        $importProcessor->expects($this->any())->method('getEavAttributeByEntityTypeIdAndAttributeCode')->willReturnCallback(function ($entityTypeId, $attributeCode) use ($attributes) {
            return $attributes[$attributeCode];
        });

        // initialize the mock for the CSV serializer
        $valueCsvSerializer = new ValueCsvSerializer();
        $valueCsvSerializer->init($mockConfiguration = $this->getMockCsvConfiguration());
        $valueCsvSerializerFactory = $this->getMockBuilder(ConfigurationAwareSerializerFactoryInterface::class)->getMock();
        $valueCsvSerializerFactory->expects($this->any())->method('createSerializer')->willReturn($valueCsvSerializer);

        // initialize the additional attribute serializer to be tested
        $this->additionalAttributeSerializer = new AdditionalAttributeCsvSerializer($this->getMockConfiguration(), $importProcessor, $valueCsvSerializerFactory);
        $this->additionalAttributeSerializer->init($mockConfiguration);
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeEmptyArrayWithSuccess()
    {
        $this->assertEquals(null, $this->additionalAttributeSerializer->serialize(array()));
    }

    /**
     * Tests if the serialize() method returns the serialized value.
     *
     * @return void
     */
    public function testSerializeWithSuccess()
    {
        $this->assertEquals('ac_01=ov_01,ac_02=ov_02', $this->additionalAttributeSerializer->serialize(array('ac_01' => 'ov_01', 'ac_02' => 'ov_02')));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeEmptyArrayWithSuccess()
    {
        $this->assertEquals(array(), $this->additionalAttributeSerializer->unserialize(null));
    }

    /**
     * Tests if the unserialize() method returns the serialized value.
     *
     * @return void
     */
    public function testUnserializeWithSuccess()
    {
        $this->assertEquals(array('ac_01' => 'ov_01', 'ac_02' => 'ov_02'), $this->additionalAttributeSerializer->unserialize('"ac_01=ov_01","ac_02=ov_02"'));
    }

    /**
     * Tests if the unserialize() method returns the serialized value, if only one value is available.
     *
     * @return void
     */
    public function testUnserializeSingleAdditionalAttribute()
    {
        $this->assertEquals(array('delivery_date_1' => '2019011'), $this->additionalAttributeSerializer->unserialize('"delivery_date_1=2019011"'));
    }

    /**
     * Tests if the unserialize() method with simple values for a boolean, select and multiselect attribute.
     *
     * @return void
     */
    public function testUnserializeWithoutValueDelimiter()
    {

        // initialize the serialized value
        $value = 'my_boolean_attribute=true,my_select_attribute=selected_value_01,my_multiselect_attribute=multiselected_value_01|multiselected_value_02';

        // initialize the expected result
        $values = array(
            'my_boolean_attribute'     => true,
            'my_select_attribute'      => 'selected_value_01',
            'my_multiselect_attribute' => array('multiselected_value_01', 'multiselected_value_02'),
        );

        // unserialize the value and assert the result
        $this->assertSame($values, $this->additionalAttributeSerializer->unserialize($value));
    }

    /**
     * Tests if the unserialize() method with enclosed simple values for a boolean, select and multiselect attribute.
     *
     * @return void
     */
    public function testUnserializeWithValueDelimiter()
    {

        // initialize the serialized value
        $value = '"my_boolean_attribute=true","my_select_attribute=selected_value_01","my_multiselect_attribute=multiselected_value_01|multiselected_value_02"';

        // initialize the expected result
        $values = array(
            'my_boolean_attribute'     => true,
            'my_select_attribute'      => 'selected_value_01',
            'my_multiselect_attribute' => array('multiselected_value_01', 'multiselected_value_02'),
        );

        // unserialize the value and assert the result
        $this->assertSame($values, $this->additionalAttributeSerializer->unserialize($value));
    }

    /**
     * Tests if the unserialize() method with partially enclosed values for a boolean, select and multiselect attribute that may contain a comma.
     *
     * @return void
     */
    public function testUnserializeWithPartialValueDelimiterAndValuesWithComma()
    {

        // initialize the serialized value
        $value = 'my_boolean_attribute=true,"my_select_attribute=selected_value,01","my_multiselect_attribute=multiselected_value,01|multiselected_value,02"';

        // initialize the expected result
        $values = array(
            'my_boolean_attribute'     => true,
            'my_select_attribute'      => 'selected_value,01',
            'my_multiselect_attribute' => array('multiselected_value,01', 'multiselected_value,02'),
        );

        // unserialize the value and assert the result
        $this->assertSame($values, $this->additionalAttributeSerializer->unserialize($value));
    }

    /**
     * Tests if the unserialize() method with enclosed simple values for a boolean, select and multiselect attribute.
     *
     * @return void
     */
    public function testUnserializeWithoutValueDelimiterAndManyAttributes()
    {

        // initialize the serialized value
        $value = '"""ClothingSize=Eine Größe XXL"",""Colours=Gelb"",""Description=Schutzoverall"",""FlagNew=Nein"",""FlagSample=Ja"",""Manufacturer=DS SafetyWear"",""Material=Vliesstoff"",""MergeUomFactor=Stück"",""Packaging=Stück"",""PublishTo=Web & Catalogue"",""Type=Mit Kapuze, verklebten Nähten und extra Polyethylenbeschichtung"",""BulletText2=<strong>195603:</strong><br>Mit Kapuze, Reißverschluss mit Abdeckblende und Gummizügen an Hosenbeinen, Armen und Kapuze. CE-Kategorie III, Typ 5, 6.<br><strong>Farbe:</strong>&nbsp;Weiß<br><strong>Größen:</strong> M, L, XL, XXL<br><br><strong>195608:</strong><br>Ausführung wie <strong>195603</strong>, jedoch mit verklebten Nähten für verbesserten Schutz. CE-Kategorie III, Typ 4, 5, 6.<br><strong>Farbe:</strong>&nbsp;Weiß<br><strong>Größen:&nbsp;</strong>M, L, XL, XXL<br><br><strong>195609:</strong><br>Ausführung wie <strong>195608</strong>, jedoch mit zusätzlicher Polyethylenbeschichtung für noch höheren Schutz, z.B. gegen aggressive Flüssigkeiten oder Sprühnebel. Geeignet für den Einsatz bei kontaminationsgefährlichen Arbeiten. CE-Kategorie III, Typ 3, 4, 5.<br><strong>Farbe:&nbsp;</strong>Gelb<br><strong>Größen:&nbsp;</strong>L, XL, XXL"",""Category1Header=Erste Hilfe & Arbeitsschutzausrüstung"",""Category3Header=Schutzkleidung"",""Legend=Schutzoveralls"",""MainlinePageNumber=U-370"",""Properties=CE-Kategorie III, Typ 3, 4, 5"",""PubCodeRankingValue=13825"",""SpaceCode=CJE: TYVEK-SCHUTZKLEIDUNG"",""StyleNo=195609"",""StyleNoHeader=Artikel-<br>nummer"",""SubHeader=Optimaler Schutz vor Staub, Farbe, Schadstoffen"",""TableHead1=Geben Sie bitte die gewünschte Größe an."",""YNumberMaterial=Y2932705"",""SP_STATUS=CURRENT"""';

        // create and initialize the CSV value serializer
        $valueCsvSerializer = new ValueCsvSerializer();
        $valueCsvSerializer->init($this->getMockCsvConfiguration());

        // initialize the expected result
        $values = array(
            'ClothingSize'        => 'Eine Größe XXL',
            'Colours'             => 'Gelb',
            'Description'         => 'Schutzoverall',
            'FlagNew'             => 'Nein',
            'FlagSample'          => 'Ja',
            'Manufacturer'        => 'DS SafetyWear',
            'Material'            => 'Vliesstoff',
            'MergeUomFactor'      => 'Stück',
            'Packaging'           => 'Stück',
            'PublishTo'           => 'Web & Catalogue',
            'Type'                => 'Mit Kapuze, verklebten Nähten und extra Polyethylenbeschichtung',
            'BulletText2'         => '<strong>195603:</strong><br>Mit Kapuze, Reißverschluss mit Abdeckblende und Gummizügen an Hosenbeinen, Armen und Kapuze. CE-Kategorie III, Typ 5, 6.<br><strong>Farbe:</strong>&nbsp;Weiß<br><strong>Größen:</strong> M, L, XL, XXL<br><br><strong>195608:</strong><br>Ausführung wie <strong>195603</strong>, jedoch mit verklebten Nähten für verbesserten Schutz. CE-Kategorie III, Typ 4, 5, 6.<br><strong>Farbe:</strong>&nbsp;Weiß<br><strong>Größen:&nbsp;</strong>M, L, XL, XXL<br><br><strong>195609:</strong><br>Ausführung wie <strong>195608</strong>, jedoch mit zusätzlicher Polyethylenbeschichtung für noch höheren Schutz, z.B. gegen aggressive Flüssigkeiten oder Sprühnebel. Geeignet für den Einsatz bei kontaminationsgefährlichen Arbeiten. CE-Kategorie III, Typ 3, 4, 5.<br><strong>Farbe:&nbsp;</strong>Gelb<br><strong>Größen:&nbsp;</strong>L, XL, XXL',
            'Category1Header'     => 'Erste Hilfe & Arbeitsschutzausrüstung',
            'Category3Header'     => 'Schutzkleidung',
            'Legend'              => 'Schutzoveralls',
            'MainlinePageNumber'  => 'U-370',
            'Properties'          => 'CE-Kategorie III, Typ 3, 4, 5',
            'PubCodeRankingValue' => '13825',
            'SpaceCode'           => 'CJE: TYVEK-SCHUTZKLEIDUNG',
            'StyleNo'             => '195609',
            'StyleNoHeader'       => 'Artikel-<br>nummer',
            'SubHeader'           => 'Optimaler Schutz vor Staub, Farbe, Schadstoffen',
            'TableHead1'          => 'Geben Sie bitte die gewünschte Größe an.',
            'YNumberMaterial'     => 'Y2932705',
            'SP_STATUS'           => 'CURRENT'
        );

        // unserialize the value first time (simulate M2IF framework)
        $unserialized = $valueCsvSerializer->unserialize($value);

        // unserialize the value and assert the result
        $this->assertSame($values, $this->additionalAttributeSerializer->unserialize(array_shift($unserialized)));
    }

    /**
     * Tests if the serialize() method with simple values for a boolean, select and multiselect attribute.
     *
     * @return void
     */
    public function testSerializeWithValueDelimiters()
    {

        // initialize the expected result
        $value = 'my_boolean_attribute=true,my_select_attribute=selected_value_01,my_multiselect_attribute=multiselected_value_01|multiselected_value_02';

        // initialize the array with the values to serializer
        $values = array(
            'my_boolean_attribute'     => true,
            'my_select_attribute'      => 'selected_value_01',
            'my_multiselect_attribute' => array('multiselected_value_01', 'multiselected_value_02')
        );

        // serialize the values and assert the result
        $this->assertSame($value, $this->additionalAttributeSerializer->serialize($values));
    }

    /**
     * Tests if the serialize() method with complex, commaseparated + delimited values for a text field.
     *
     * @return void
     */
    public function testSerializeWithCommaSeparatedAndValueDelimitedValues()
    {

        // initialize the expected result
        $value = '"Application=Empfangshallen,Postabteilungen,Arztpraxen,Verkaufsräume,Reisebüros","BulletText2=<strong>Material:</strong>&nbsp;Polyethylen, transparent <br><strong>Fachtiefe:</strong>&nbsp;45 mm <br><strong>Lieferumfang:</strong>&nbsp;inkl. Befestigungsmaterial"';

        // initialize the array with the values to serializer
        $values = array(
            'Application' => 'Empfangshallen,Postabteilungen,Arztpraxen,Verkaufsräume,Reisebüros',
            'BulletText2' => '<strong>Material:</strong>&nbsp;Polyethylen, transparent <br><strong>Fachtiefe:</strong>&nbsp;45 mm <br><strong>Lieferumfang:</strong>&nbsp;inkl. Befestigungsmaterial'
        );

        // serialize the values and assert the result
        $this->assertSame($value, $this->additionalAttributeSerializer->serialize($values));
    }
}
