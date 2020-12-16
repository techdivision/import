<?php

/**
 * TechDivision\Import\Utils\UrlKeyUtilTest
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
* @copyright 2020 TechDivision GmbH <info@techdivision.com>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Utils;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Test class for the URL key utility.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlKeyUtilTest extends TestCase
{

    /**
     * The utility we want to test.
     *
     * @var \TechDivision\Import\Utils\UrlKeyUtilInterface
     */
    protected $urlKeyUtil;

    /**
     * The array with the dummy data.
     *
     * @var array
     */
    protected $urlRewrites = array(
        'joust-duffle-bag' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            ),
            1 => array(
                MemberNames::STORE_ID => 1,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            )
        ),
        'gear/joust-duffle-bag' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            ),
            1 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            )
        ),
        'gear/bags/joust-duffle-bag' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            ),
            1 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234
            )
        ),
        'joust-duffle-bag-1' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 301,
                MemberNames::ENTITY_ID => 1234
            )
        ),
        'joust-duffle-bag-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 301,
                MemberNames::ENTITY_ID => 1234
            )
        ),
        'joust-duffle-bag-2-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1235
            )
        ),
        'joust-duffle-bag-2-2-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1236
            )
        ),
        'gear/bags/duffle-bags' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1237
            )
        ),
        'gear/duffle-bags' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1238
            )
        )
    );

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // mock the URL key aware processor instance
        $urlKeyAwareProcessor = $this->getMockBuilder(UrlKeyAwareProcessorInterface::class)->getMock();

        // initialize the utility we want to test
        $this->urlKeyUtil = $this->getMockBuilder(UrlKeyUtil::class)
            ->setMethods(array('loadUrlRewriteByRequestPathAndStoreId'))
            ->setConstructorArgs(array($urlKeyAwareProcessor))
            ->getMock();

        // mock the loadUrlRewriteByRequestPathAndStoreId() method
        $this->urlKeyUtil
            ->expects($this->any())
            ->method('loadUrlRewriteByRequestPathAndStoreId')
            ->will($this->returnCallback(function ($arg1, $arg2) {
                return isset($this->urlRewrites[$arg1][$arg2]) ? $this->urlRewrites[$arg1][$arg2] : null;
            }));
    }

    /**
     * Create and return a mock subject instance initialized with
     * the passed data.
     *
     * @param int $entityId The entity used to mock the isUrlKeyOf() method
     * @param int $storeId  The store ID used to mock the getRowStoreId() method
     *
     * @return \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface The mocked subject instance
     */
    protected function getMockSubject(int $entityId = 1234,  int $storeId = 0) : UrlKeyAwareSubjectInterface
    {

        // the mock subject
        $mockSubject = $this->getMockBuilder(UrlKeyAwareSubjectInterface::class)
            ->setMethods(get_class_methods(UrlKeyAwareSubjectInterface::class))
            ->getMock();

        // mock the method that returns the ID of the actual store
        $mockSubject->expects($this->any())
            ->method('getRowStoreId')
            ->willReturn($storeId);

        // mock the method to query whether or not he URL rewrite
        // is related with the entity with the passed entity ID
        $mockSubject->expects($this->any())
            ->method('isUrlKeyOf')
            ->will($this->returnCallback(function ($arg1) use ($entityId, $storeId) {
                return $arg1[MemberNames::ENTITY_ID]     === $entityId
                    && $arg1[MemberNames::STORE_ID]      === $storeId
                    && $arg1[MemberNames::REDIRECT_TYPE] === 0;
            }));

        // return the mock subject
        return $mockSubject;
    }

    /**
     * Test if makeUnique() method returns the same key because it has not been used yet.
     *
     * @return void
     * @see case-01-01
     */
    public function testMakeUniqueWithNewKey() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject();

        // assert the unique URL key
        $this->assertSame(
            'unknown-key',
            $this->urlKeyUtil->makeUnique($mockSubject, 'unknown-key')
        );
    }

    /**
     * Test if makeUnique() method returns the same key if the product exits and already has the same key.
     *
     * @return void
     * @see case-01-02
     */
    public function testMakeUniqueWithSameKeyAndStoreAndEntity() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject();

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag',
            $this->urlKeyUtil->makeUnique($mockSubject, 'joust-duffle-bag')
        );
    }

    /**
     * Test if makeUnique() method returns the same key if the category exits and already has the same key.
     *
     * @return void
     * @see case-04-02
     */
    public function testMakeUniqueWithSameKeyAndMultipathAndStoreAndEntity() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(1237);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $this->urlKeyUtil->makeUnique($mockSubject, 'duffle-bags', 'gear/bags')
        );
    }

    /**
     * Test if makeUnique() method returns the same key and entity but another store.
     *
     * @return void
     * @see case-01-02
     */
    public function testMakeUniqueWithSameKeyAndEntityAndAnotherStore() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(1234, 1);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag',
            $this->urlKeyUtil->makeUnique($mockSubject, 'joust-duffle-bag')
        );
    }

    /**
     * Test if makeUnique() method raises the counter for an existing key and store but a different entity.
     *
     * @return void
     * @see case-01-03
     */
    public function testMakeUniqueWithSameKeyAndStoreButDifferentEntity() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(4321);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-3',
            $this->urlKeyUtil->makeUnique($mockSubject, 'joust-duffle-bag')
        );
    }

    /**
     * Test if makeUnique() method returns a key with the counter raisen when a related category
     * with the same path and key exists.
     *
     * @return void
     * @see case-01-04
     */
    public function testMakeUniqueWithRaisedCounterWhenARelatedCatgoryWithTheSameKeyAndPathExists() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(4321);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-3',
            $this->urlKeyUtil->makeUnique($mockSubject, 'joust-duffle-bag', 'gear')
        );
    }

    /**
     * Test if makeUnique() method returns the given key if a not related category with the same key exists.
     *
     * @return void
     * @see case-01-05
     */
    public function testMakeUniqueIfANotRelatedCategoryWithTheSameKeyExists() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(4321);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $this->urlKeyUtil->makeUnique($mockSubject, 'duffle-bags', 'women')
        );
    }

    /**
     * Test if makeUnique() method returns the same key, multipath and store but a different entity.
     *
     * This can happen, when someone tries to create a category with the same URL path/key as an
     * already exisiting category/product URL rewrite has.
     *
     * @return void
     * @see case-01-06
     */
    public function testMakeUniqueWithSameKeyAndMultipathAndStoreButDifferentEntity() : void
    {

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(5432, 1);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-1',
            $this->urlKeyUtil->makeUnique($mockSubject, 'joust-duffle-bag', 'gear/bags')
        );
    }

    /**
     * Test if makeUnique() method returns the same key, multipath and store but a different entity.
     *
     * @return void
     */
    public function testMakeUniqueWithMultipleInvocationsSameKeyAndMultipathAndStoreButDifferentEntity() : void
    {

        // skip the test as the ...
        $this->markTestSkipped('functionality has not yet been implemented');

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(1237);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $this->urlKeyUtil->makeUnique($mockSubject, 'duffle-bags', 'gear/bags')
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8321);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags-1',
            $this->urlKeyUtil->makeUnique($mockSubject, 'duffle-bags', 'gear/bags')
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8322);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags-2',
            $this->urlKeyUtil->makeUnique($mockSubject, 'duffle-bags', 'gear/bags')
        );
    }
}
