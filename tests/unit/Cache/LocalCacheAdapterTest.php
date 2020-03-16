<?php

/**
 * TechDivision\Import\Cache\LocalCacheAdapterTest
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

namespace TechDivision\Import\Cache;
;
use TechDivision\Import\Utils\CacheKeyUtil;
use TechDivision\Import\ConfigurationInterface;

/**
 * Test class for the local cache adapter.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LocalCacheAdapterTestt extends \PHPUnit_Framework_TestCase
{

    /**
     * The exportable trait that has to be tested.
     *
     * @var \TechDivision\Import\Cache\LocalCacheAdapter
     */
    protected $localCacheAdapter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {

        // create the mock configuration
        $mockConfiguration = $this->getMockBuilder(ConfigurationInterface::class)
            ->setMethods(array('getSerial'))
            ->getMockForAbstractClass();
        // mock the methods
        $mockConfiguration
            ->expects($this->any())
            ->method('getSerial')
            ->willReturn(uniqid('test', true));

        // create a new cache key instance
        $cacheKeyUtil = new CacheKeyUtil($mockConfiguration);

        // initialize the cache adapter we want to test
        $this->localCacheAdapter = new LocalCacheAdapter($cacheKeyUtil);

        // load the test data
        $values = $this->provideTestdata();
        // pre-initialize the cacha adapter with the values
        foreach ($values as $value) {
            // explode key and reference
            list ($uniqueKey, $ref) = $value;
            // prepare the array with the references
            $references = array($ref => $uniqueKey);
            // add the value to the cache
            $this->localCacheAdapter->toCache($uniqueKey, $value, $references);
        }
    }

    /**
     * Test the removeCache() method.
     *
     * @param string $uniqueKey The unique key of the item that has to be removed from the cache
     * @param string $ref       The reference of the item that has also to be removed
     *
     * @return void
     * @dataProvider provideTestdata
     */
    public function testRemoveCache(string $uniqueKey, string $ref)
    {

        // remove the value from the cache
        $this->localCacheAdapter->removeCache($uniqueKey);

        // query whether or not the value is NOT available either per unique key or reference
        $this->assertFalse($this->localCacheAdapter->isCached($uniqueKey));
        $this->assertFalse($this->localCacheAdapter->isCached($ref));
    }

    /**
     * Test the toCache() method.
     *
     * @param string $uniqueKey The unique key of the item that has to be cached
     * @param string $ref       The reference of the item that has to be cached
     *
     * @return void
     * @dataProvider provideTestdata
     */
    public function testToCache(string $uniqueKey, string $ref)
    {

        // create the array with thereferences
        $references = array($ref => $uniqueKey);

        // add the value to the cache
        $this->localCacheAdapter->toCache($uniqueKey, array($uniqueKey, $ref), $references, array(), true);

        // query whether or not the value is available either per unique key or reference
        $this->assertTrue($this->localCacheAdapter->isCached($uniqueKey));
        $this->assertTrue($this->localCacheAdapter->isCached($ref));;
    }

    /**
     * Data provider for the methods that has to be tested.
     *
     * @return \Generator The generator instance
     */
    public function provideTestdata()
    {
        for ($i = 0; $i < 10; $i++) {
            yield array("key-$i", "ref-$i");
        }
    }
}
