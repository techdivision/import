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
        $action = $this->getMockForAbstractClass('TechDivision\Import\Actions\AbstractAction');

        // test the setter/getter for the persist processor
        $action->setPersistProcessor($mockPersistProcessor);
        $this->assertSame($mockPersistProcessor, $action->getPersistProcessor());
    }
}
