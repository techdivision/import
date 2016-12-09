<?php

/**
 * TechDivision\Import\Actions\AbstractAction
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

namespace TechDivision\Import\Actions;

/**
 * Test class for the abstract action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test's the getter/setter for the persist processor.
     *
     * @return void
     */
    public function testSetGetPersistProcessor()
    {

        // create a persist processor mock instance
        $mockPersistProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                     ->setMethods(get_class_methods($processorInterface))
                                     ->getMock();

        // create a mock for the abstract action
        $mockAction = $this->getMockForAbstractClass('TechDivision\Import\Actions\AbstractAction');

        // test the setter/getter for the persist processor
        $mockAction->setPersistProcessor($mockPersistProcessor);
        $this->assertSame($mockPersistProcessor, $mockAction->getPersistProcessor());
    }

    /**
     * Test's the getter/setter for the remove processor.
     *
     * @return void
     */
    public function testSetGetRemoveProcessor()
    {

        // create a remove processor mock instance
        $mockRemoveProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                    ->setMethods(get_class_methods($processorInterface))
                                    ->getMock();

        // create a mock for the abstract action
        $mockAction = $this->getMockForAbstractClass('TechDivision\Import\Actions\AbstractAction');

        // test the setter/getter for the remove processor
        $mockAction->setRemoveProcessor($mockRemoveProcessor);
        $this->assertSame($mockRemoveProcessor, $mockAction->getRemoveProcessor());
    }

    /**
     * Test's the persist() method successfull.
     *
     * @return void
     */
    public function testPersistWithSuccess()
    {

        // create a persist processor mock instance
        $mockPersistProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                     ->setMethods(get_class_methods($processorInterface))
                                     ->getMock();
        $mockPersistProcessor->expects($this->once())
                             ->method('execute')
                             ->with($row = array())
                             ->willReturn(null);

        // create a mock for the abstract action
        $mockAction = $this->getMockBuilder('TechDivision\Import\Actions\AbstractAction')
                           ->setMethods(array('getPersistProcessor'))
                           ->getMock();
        $mockAction->expects($this->once())
                   ->method('getPersistProcessor')
                   ->willReturn($mockPersistProcessor);

        // test the persist() method
        $this->assertNull($mockAction->persist($row));
    }

    /**
     * Test's the remove() method successfull.
     *
     * @return void
     */
    public function testRemoveWithSuccess()
    {

        // create a persist processor mock instance
        $mockRemoveProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                    ->setMethods(get_class_methods($processorInterface))
                                    ->getMock();
        $mockRemoveProcessor->expects($this->once())
                            ->method('execute')
                            ->with($row = array())
                            ->willReturn(null);

        // create a mock for the abstract action
        $mockAction = $this->getMockBuilder('TechDivision\Import\Actions\AbstractAction')
                           ->setMethods(array('getRemoveProcessor'))
                           ->getMock();
        $mockAction->expects($this->once())
                   ->method('getRemoveProcessor')
                   ->willReturn($mockRemoveProcessor);

        // test the remove() method
        $this->assertNull($mockAction->remove($row));
    }

    public function testThatFails()
    {
        $this->fail('Should fail for testing purposes');
    }
}
