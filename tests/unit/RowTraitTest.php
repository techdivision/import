<?php

/**
 * TechDivision\Import\RowTraitTest
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

use PHPUnit\Framework\TestCase;

/**
 * Test class for the row trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RowTraitTest extends TestCase
{

    /**
     * The exportable trait that has to be tested.
     *
     * @var \TechDivision\Import\RowTrait
     */
    protected $rowTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->rowTrait = $this->getMockForAbstractClass('TechDivision\Import\RowTraitImpl');
    }

    /**
     * Test the set/getValue() method.
     *
     * @return void
     */
    public function testGetValueWithNumber()
     {

         // mock the available haeders
         $this->rowTrait->setHeaders(array($name = 'test' => 0));

         // query whether or not the given value is available and a number
         $this->rowTrait->setValue($name, $value = 1);
         $this->assertSame($value, $this->rowTrait->getValue($name));
     }

    /**
     * Test the set/getValue() method converting the value by invoking a callback.
     *
     * @return void
     */
    public function testGetValueWithStringConvertedToNumberByCallback()
     {

         // mock the available haeders
         $this->rowTrait->setHeaders(array($name = 'test' => 0));

         // query whether or not the given value is available and a number
         $this->rowTrait->setValue($name, '100');
         $this->assertSame(100, $this->rowTrait->getValue($name, null, function ($value) {
            return (integer) $value;
         }));
     }

    /**
     * Test the getValue() method with a default value.
     *
     * @return void
     */
    public function testGetDefaultValue()
     {
         $this->assertSame(100, $this->rowTrait->getValue('test', 100));
     }

    /**
     * Test the hasValue() method without a header available.
     *
     * @return void
     */
    public function testHasValueWithMissingValueWithoutHeader()
     {
         $this->assertFalse($this->rowTrait->hasValue('test'));
     }

    /**
     * Test the hasValue() method with a header available.
     *
     * @return void
     */
    public function testHasValueWithMissingValueWithHeader()
     {
         $this->rowTrait->addHeader($name = 'test');
         $this->assertFalse($this->rowTrait->hasValue($name));
     }
}
