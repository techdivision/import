<?php

/**
 * TechDivision\Import\Utils\UrlKeyUtilTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2020 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Utils;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Test class for the URL key utility.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlKeyUtilTest extends TestCase
{

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
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            ),
            1 => array(
                MemberNames::STORE_ID => 1,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'gear/joust-duffle-bag' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            ),
            1 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'gear/bags/joust-duffle-bag' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            ),
            1 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'joust-duffle-bag-1' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 301,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'joust-duffle-bag-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 301,
                MemberNames::ENTITY_ID => 1234,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'joust-duffle-bag-2-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1235,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'joust-duffle-bag-2-2-2' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1236,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'gear/bags/duffle-bags' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1237,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
            )
        ),
        'gear/duffle-bags' => array(
            0 => array(
                MemberNames::STORE_ID => 0,
                MemberNames::REDIRECT_TYPE => 0,
                MemberNames::ENTITY_ID => 1238,
                MemberNames::ENTITY_TYPE => UrlRewriteEntityType::PRODUCT
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
    protected function getMockUrlKeyUtil($urlRewriteEntityType = UrlRewriteEntityType::PRODUCT) : UrlKeyUtilInterface
    {

        // mock the URL key aware processor instance
        $urlKeyAwareProcessor = $this->getMockBuilder(UrlKeyAwareProcessorInterface::class)->getMock();

        // mock the persist method
        $urlKeyAwareProcessor
            ->expects($this->any())
            ->method('persistUrlRewrite')
            ->will($this->returnCallback(function ($arg) {
                $this->urlRewrites[$arg[MemberNames::REQUEST_PATH]][$arg[MemberNames::STORE_ID]] = $arg;
            }));

        // mock the core config data loader instance
        $coreConfigDataLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $coreConfigDataLoader->expects($this->any())->method('load')->willReturn(null);

        // mock the store ID loader instance
        $storeIdLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $storeIdLoader->expects($this->any())->method('load')->willReturn(array(0 => 0, 1 => 1));

        // mock the URL rewrite entity type
        $mockUrlRewriteEntityType = new UrlRewriteEntityType($urlRewriteEntityType);

        // initialize the utility we want to test
        $urlKeyUtil = $this->getMockBuilder(UrlKeyUtil::class)
            ->setMethods(array('loadUrlRewriteByRequestPathAndStoreId'))
            ->setConstructorArgs(
                array(
                    $urlKeyAwareProcessor,
                    $coreConfigDataLoader,
                    $storeIdLoader,
                    $mockUrlRewriteEntityType
                )
            )
            ->getMock();

        // mock the loadUrlRewriteByRequestPathAndStoreId() method
        $urlKeyUtil
            ->expects($this->any())
            ->method('loadUrlRewriteByRequestPathAndStoreId')
            ->will($this->returnCallback(function ($arg1, $arg2) {
                return isset($this->urlRewrites[$arg1][$arg2]) ? $this->urlRewrites[$arg1][$arg2] : null;
            }));

        // return the instance
        return $urlKeyUtil;
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

        // mock the method that returns the ID of the actual store
        $mockSubject->expects($this->any())
            ->method('getEntityTypeCode')
            ->willReturn(EntityTypeCodes::CATALOG_PRODUCT);

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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 1234);

        // assert the unique URL key
        $this->assertSame(
            'unknown-key',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'unknown-key')
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 1234);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'joust-duffle-bag')
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 1237);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'duffle-bags', array('gear/bags'))
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 1234);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'joust-duffle-bag')
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 4321);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-3',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'joust-duffle-bag')
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 4321);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-3',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'joust-duffle-bag', array('gear'))
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 4321);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'duffle-bags', array('women'))
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

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 5432);

        // assert the unique URL key
        $this->assertSame(
            'joust-duffle-bag-1',
            $this->getMockUrlKeyUtil()->makeUnique($mockSubject, $entity, 'joust-duffle-bag', array('gear/bags'))
        );
    }

    /**
     * Test if makeUnique() method returns a different key if we pass the same one but with different entities.
     *
     * @return void
     */
    public function testMakeUniqueWithMultipleInvocationsSameKeyButDifferentEntity() : void
    {

        // load the URL key utility we want to test
        $urlKeyUtil = $this->getMockUrlKeyUtil();

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8765);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8765);

        // assert the unique URL key
        $this->assertSame(
            'testproduct',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'testproduct', array())
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8764);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8764);

        // assert the unique URL key
        $this->assertSame(
            'testproduct-1',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'testproduct', array())
        );
    }

    /**
     * Test if makeUnique() method returns the same key, multipath and store but a different entity.
     *
     * @return void
     */
    public function testMakeUniqueWithMultipleInvocationsSameKeyAndMultipathAndStoreButDifferentEntity() : void
    {

        // load the URL key utility we want to test
        $urlKeyUtil = $this->getMockUrlKeyUtil();

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(1237);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 1237);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'duffle-bags', array('gear/bags'))
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8321);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8321);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags-1',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'duffle-bags', array('gear/bags'))
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8322);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8322);

        // assert the unique URL key
        $this->assertSame(
            'duffle-bags-2',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'duffle-bags', array('gear/bags'))
        );
    }

    /**
     * Test if makeUnique() method returns a different key if we pass the same one but with different entities.
     *
     * @return void
     */
    public function testMakeUniqueWithMultipleInvocationsSameKeyButDifferentCategoryEntities() : void
    {

        // load the URL key utility we want to test
        $urlKeyUtil = $this->getMockUrlKeyUtil(UrlRewriteEntityType::CATEGORY);

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8765);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8765);

        // assert the unique URL key
        $this->assertSame(
            'testcategory',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'testcategory', array())
        );

        // load the mock subject instance
        $mockSubject = $this->getMockSubject(8766);

        // initialize the entity
        $entity = array(MemberNames::ENTITY_ID => 8766);

        // assert the unique URL key
        $this->assertSame(
            'testcategory',
            $urlKeyUtil->makeUnique($mockSubject, $entity, 'testcategory', array('testcategory'))
        );
    }
}
