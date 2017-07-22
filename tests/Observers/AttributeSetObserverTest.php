<?php

/**
 * TechDivision\Import\Observers\AttributeSetObserverTest
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\ColumnKeys;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeSetObserverTest extends \PHPUnit_Framework_TestCase
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
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
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
