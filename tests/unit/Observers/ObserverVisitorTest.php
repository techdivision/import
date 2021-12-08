<?php

/**
 * TechDivision\Import\Observers\ObserverVisitorTest
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

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ObserverVisitorTest extends TestCase
{

    /**
     * Test the handle() method.
     *
     * @return void
     */
    public function testHandle()
    {

        $observers = array(
            array(
                $type = 'import' => array(
                    $id = 'a-observer-id'
                )
            )
        );

        $mockObserver = $this->getMockBuilder('TechDivision\Import\Observers\ObserverInterface')
                             ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverInterface'))
                             ->getMock();

        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\TaggedContainerInterface')
                              ->setMethods(get_class_methods('Symfony\Component\DependencyInjection\TaggedContainerInterface'))
                              ->getMock();
        $mockContainer->expects($this->once())
                      ->method('get')
                      ->with($id)
                      ->willReturn($mockObserver);

        $observerVisitor = new ObserverVisitor($mockContainer);

        $mockConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                  ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                  ->getMock();
        $mockConfiguration->expects($this->once())
                          ->method('getObservers')
                          ->willReturn($observers);

        $mockSubject = $this->getMockBuilder('TechDivision\Import\Subjects\SubjectInterface')
                            ->setMethods(get_class_methods('TechDivision\Import\Subjects\SubjectInterface'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getConfiguration')
                    ->willReturn($mockConfiguration);
        $mockSubject->expects($this->once())
                    ->method('registerObserver')
                    ->with($mockObserver, $type)
                    ->willReturn(null);

        $observerVisitor->visit($mockSubject);
    }
}
