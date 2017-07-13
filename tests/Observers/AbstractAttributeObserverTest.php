<?php

/**
 * TechDivision\Import\Observers\AbstractAttributeObserverTest
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
class AbstractAttributeObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AttributeObserverTraitImpl
     */
    protected $attributeObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->attributeObserver = $this->getMockBuilder('TechDivision\Import\Observers\AbstractAttributeObserver')
                                        ->getMockForAbstractClass();
    }

    /**
     * Test the handle() method with a missing attribute.
     *
     * @return void
     */
    public function testHandleWithInvalidAttribute()
    {

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Observers\EavSubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Observers\EavSubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('prepareStoreViewCode')
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('getAttributes')
                    ->willReturn(array());
        $mockSubject->expects($this->once())
                    ->method('getHeaders')
                    ->willReturn(array(ColumnKeys::STORE_VIEW_CODE => 0));
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn(array(0 => 'en_US'));

        // invoke the handle method
        $this->attributeObserver->handle($mockSubject);
    }
}
