<?php

/**
 * TechDivision\Import\Utils\CategoryPathUtilTest
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
* @copyright 2020 TechDivision GmbH <info@techdivision.com>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Utils;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Serializer\Csv\ValueCsvSerializer;
use TechDivision\Import\Serializer\Csv\AbstractSerializerTest;

/**
 * Test class for the category path utility.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CategoryPathUtilTest extends AbstractSerializerTest
{

    /**
     * The utility we want to test.
     *
     * @var \TechDivision\Import\Utils\CategoryPathUtilInterface
     */
    protected $categoryPathUtil;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        $valueCsvSerializer = new ValueCsvSerializer();
        $valueCsvSerializer->init($this->getMockSerializerConfiguration());

        $this->categoryPathUtil = new CategoryPathUtil($valueCsvSerializer);
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testImplode() : void
    {
        $this->assertSame(
            '"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"" 2"',
            $this->categoryPathUtil->implode(array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft" 2'))
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testImplode2() : void
    {


        $categories = array(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft"'),
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft" 2')
        );


        $cats = array();
        foreach ($categories as $elements) {
            $cats[] = $this->categoryPathUtil->implode($elements);
        }

        $this->assertSame(
            '"""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft""""""","""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""" 2"""',
            $result = $this->categoryPathUtil->serialize($cats)
        );

        $this->assertSame(
            '"""""""Default Category""""/""""Etiketten und Prüfplaketten""""/Prüfplaketten/""""Prüfplaketten """"""""Nächster Prüftermin / Geprüft"""""""""""""",""""""Default Category""""/""""Etiketten und Prüfplaketten""""/Prüfplaketten/""""Prüfplaketten """"""""Nächster Prüftermin / Geprüft"""""""" 2"""""""',
            $this->categoryPathUtil->serialize(array($result))
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testImplode3() : void
    {


        $categories = array(
            array('Default Category', 'Example Category')
        );


        $cats = array();
        foreach ($categories as $elements) {
            $cats[] = $this->categoryPathUtil->implode($elements);
        }

        $this->assertSame(
            '"""Default Category""/""Example Category"""',
            $result = $this->categoryPathUtil->serialize($cats)
        );

        $this->assertSame(
            '"""""""Default Category""""/""""Example Category"""""""',
            $this->categoryPathUtil->serialize(array($result))
        );
    }

    public function testExplode() : void
    {
        $this->assertSame(
            array('Default Category', 'Etiketten und Prüfplaketten', 'Prüfplaketten', 'Prüfplaketten "Nächster Prüftermin / Geprüft"'),
            $this->categoryPathUtil->explode('"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"""')
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testDenormalize() : void
    {
        $this->assertSame(
            '"""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""" 2"""',
            $this->categoryPathUtil->denormalize('"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"" 2"')
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testDenormalize2() : void
    {
        $this->assertSame(
            '"""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""""""',
            $this->categoryPathUtil->denormalize('"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"""')
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testDenormalize3() : void
    {
        $this->assertSame(
            '"""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""""""',
            $this->categoryPathUtil->denormalize('"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"""')
        );
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testNormalize() : void
    {
        $this->assertSame(
            '"Default Category"/"Etiketten und Prüfplaketten"/Prüfplaketten/"Prüfplaketten ""Nächster Prüftermin / Geprüft"" 2"',
            $this->categoryPathUtil->normalize('"""Default Category""/""Etiketten und Prüfplaketten""/Prüfplaketten/""Prüfplaketten """"Nächster Prüftermin / Geprüft"""" 2"""')
        );
    }
}
