<?php

/**
 * TechDivision\Import\Plugins\SubjectPluginTest
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

namespace TechDivision\Import\Plugins;

/**
 * Test class for the subject plugin implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Product\Subjects\BunchSubject
     */
    protected $subject;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock application
        $mockApplication = $this->getMockBuilder('TechDivision\Import\ApplicationInterface')
                                ->setMethods(get_class_methods('TechDivision\Import\ApplicationInterface'))
                                ->getMock();

        // create a mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\PluginConfigurationInterface')
                                        ->setMethods(get_class_methods('TechDivision\Import\Configuration\PluginConfigurationInterface'))
                                        ->getMock();

        // create a mock callback visitor
        $mockCallbackVisitor = $this->getMockBuilder('TechDivision\Import\Callbacks\CallbackVisitor')
                                    ->disableOriginalConstructor()
                                    ->setMethods(get_class_methods('TechDivision\Import\Callbacks\CallbackVisitor'))
                                    ->getMock();

        // create a mock observer visitor
        $mockObserverVisitor = $this->getMockBuilder('TechDivision\Import\Observers\ObserverVisitor')
                                    ->disableOriginalConstructor()
                                    ->setMethods(get_class_methods('TechDivision\Import\Observers\ObserverVisitor'))
                                    ->getMock();

        // initialize the subject instance
        $this->subject = new SubjectPlugin($mockApplication, $mockPluginConfiguration, $mockCallbackVisitor, $mockObserverVisitor);
    }

    /**
     * Test's if the passed file is NOT part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithNoBunch()
    {

        // initialize the prefix and the actual date
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-173_01.csv', $prefix, $actualDate), false),
            array(sprintf('import/add-update/%s_%s-174_01.csv', $prefix, $actualDate), false),
        );

        // make the protected method accessible
        $reflectionObject = new \ReflectionObject($this->subject);
        $reflectionMethod = $reflectionObject->getMethod('isPartOfBunch');
        $reflectionMethod->setAccessible(true);

        // make sure, that only the FIRST file is part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $filename));
        }
    }

    /**
     * Test's if the passed file IS part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithBunch()
    {

        // initialize the prefix and the actual date
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_02.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_03.csv', $prefix, $actualDate), true),
        );

        // make the protected method accessible
        $reflectionObject = new \ReflectionObject($this->subject);
        $reflectionMethod = $reflectionObject->getMethod('isPartOfBunch');
        $reflectionMethod->setAccessible(true);

        // make sure, that the file IS part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $reflectionMethod->invoke($this->subject, $prefix, $filename));
        }
    }
}
