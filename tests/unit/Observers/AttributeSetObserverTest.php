<?php

/**
 * TechDivision\Import\Observers\AttributeSetObserverTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Observers;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\MemberNames;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeSetObserverTest extends TestCase
{

    /**
     * The attribute set observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AttributeSetObserver
     */
    protected $attributeSetObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->attributeSetObserver = $this->getMockBuilder('TechDivision\Import\Observers\AttributeSetObserver')
                                           ->getMockForAbstractClass();
    }

    /**
     * Test the handle() method.
     *
     * @return void
     */
    public function testHandle()
    {

        // initialize the attribute set
        $attributeSet = array(
            MemberNames::ATTRIBUTE_SET_ID => 4,
            MemberNames::ENTITY_TYPE_ID => 4,
            MemberNames::ATTRIBUTE_SET_NAME => $attributeSetName = 'Default'
        );

        // prepare the row
        $row = array(
            0 => $attributeSetName
        );

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EntitySubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EntitySubjectImpl'))
                            ->getMockForAbstractClass();
        $mockSubject->expects($this->once())
                    ->method('getAttributeSetByAttributeSetName')
                    ->with($attributeSetName)
                    ->willReturn($attributeSet);
        $mockSubject->expects($this->once())
                    ->method('hasHeader')
                    ->willReturn(ColumnKeys::ATTRIBUTE_SET_CODE);
        $mockSubject->expects($this->once())
                    ->method('getHeader')
                    ->willReturn(0);
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->once())
                    ->method('setAttributeSet')
                    ->with($attributeSet)
                    ->willReturn(null);

        // invoke the handle method
        $this->attributeSetObserver->handle($mockSubject);
    }
}
