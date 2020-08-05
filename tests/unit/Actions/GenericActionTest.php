<?php

/**
 * TechDivision\Import\Actions\GenericActionTest
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

namespace TechDivision\Import\Actions;

use PHPUnit\Framework\TestCase;

/**
 * Test class for the generic action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericActionTest extends TestCase
{

    /**
     * Test's the create() method successfull.
     *
     * @return void
     */
    public function testCreateWithSuccess()
    {

        // create a persist processor mock instance
        $mockCreateProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                    ->setMethods(get_class_methods($processorInterface))
                                    ->getMock();
        $mockCreateProcessor->expects($this->once())
                            ->method('execute')
                            ->with($row = array())
                            ->willReturn(null);

        // create a mock for the abstract action
        $mockAction = $this->getMockBuilder('TechDivision\Import\Actions\GenericAction')
                           ->setMethods(array('getCreateProcessor'))
                           ->getMock();
        $mockAction->expects($this->once())
                   ->method('getCreateProcessor')
                   ->willReturn($mockCreateProcessor);

        // test the create() method
        $this->assertNull($mockAction->create($row));
    }

    /**
     * Test's the delete() method successfull.
     *
     * @return void
     */
    public function testDeleteWithSuccess()
    {

        // create a delete processor mock instance
        $mockDeleteProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Actions\Processors\ProcessorInterface')
                                    ->setMethods(get_class_methods($processorInterface))
                                    ->getMock();
        $mockDeleteProcessor->expects($this->once())
                            ->method('execute')
                            ->with($row = array())
                            ->willReturn(null);

        // create a mock for the abstract action
        $mockAction = $this->getMockBuilder('TechDivision\Import\Actions\GenericAction')
                           ->setMethods(array('getDeleteProcessor'))
                           ->getMock();
        $mockAction->expects($this->once())
                   ->method('getDeleteProcessor')
                   ->willReturn($mockDeleteProcessor);

        // test the delete() method
        $this->assertNull($mockAction->delete($row));
    }
}
