<?php

/**
 * TechDivision\Import\Observers\AbstractObserverTest
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
use TechDivision\Import\Utils\EntityStatus;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AbstractObserverTest extends TestCase
{

    /**
     * The abstract observer we want to test.
     *
     * @var \TechDivision\Import\Observers\AbstractObserver
     */
    protected $abstractObserver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {

        // mock a subject
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Subjects\SubjectInterface')
                            ->setMethods(get_class_methods('TechDivision\Import\Subjects\SubjectInterface'))
                            ->getMock();

        // the abstract mock observer
        $this->abstractObserver = $this->getMockBuilder('TechDivision\Import\Observers\AbstractObserverImpl')
                                       ->setConstructorArgs(array($mockSubject))
                                       ->getMockForAbstractClass();
    }

    /**
     * Test the getSubject() method.
     */
    public function testGetSubject()
    {
        $this->assertInstanceOf('TechDivision\Import\Subjects\SubjectInterface', $this->abstractObserver->getSubject());
    }

    /**
     * Test the initializeEntity() method.
     */
    public function testInitializeEntity()
    {

        // initialize the entity
        $entity = array('attribute' => 100);

        // prepare the expected result
        $expectedResult = array_merge($entity, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE));

        // assert that the entity has been initialized
        $this->assertSame($expectedResult, $this->abstractObserver->initializeEntity($entity));
    }

    /**
     * Test the mergeEntity() method.
     */
    public function testMergeEntity()
    {

        // initialize the entity + attributes
        $entity = array('attribute' => 100);
        $attr = array('anoterAttribute' => false);

        // prepare the expected result
        $expectedResult = array_merge($entity, $attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE));

        // assert that the entity has been initialized
        $this->assertSame($expectedResult, $this->abstractObserver->mergeEntity($entity, $attr));
    }
}
