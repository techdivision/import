<?php

/**
 * TechDivision\Import\Observers\AttributeObserverTraitMergeEntityTest
 *
 * PHP version 8
 *
 * @author    TechDivision GmbH <info@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use PHPUnit\Framework\TestCase;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\OperationNames;

/**
 * Test class for the mergeEntity() method of the attribute observer trait.
 *
 * An empty cell of a column with a default value keeps the stored value, unless the column
 * is listed in clean-up-empty-columns. The clean-up column keys hold the header positions
 * of the columns, not the attribute codes.
 *
 * @author    TechDivision GmbH <info@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeObserverTraitMergeEntityTest extends TestCase
{

    /**
     * The header positions of the test columns.
     *
     * @var array
     */
    private $headers = array('sku' => 0, 'aup_is_new' => 1, 'aup_plain' => 2);

    /**
     * Creates the observer double.
     *
     * @param array      $defaultColumnValues The columns with a default value
     * @param array|null $cleanUpColumnKeys   The header positions of the columns to clean up
     *
     * @return \TechDivision\Import\Observers\AttributeObserverTraitMergeEntityImpl The observer
     */
    private function createObserver(array $defaultColumnValues, ?array $cleanUpColumnKeys)
    {
        return new AttributeObserverTraitMergeEntityImpl($this->headers, $defaultColumnValues, $cleanUpColumnKeys);
    }

    /**
     * An empty cell of a default value column that is listed to be cleaned up deletes the value.
     *
     * @return void
     */
    public function testListedDefaultValueColumnIsDeleted()
    {
        $observer = $this->createObserver(array('aup_is_new' => ''), array($this->headers['aup_is_new']));

        $result = $observer->merge('aup_is_new', array(MemberNames::VALUE => '0'));

        $this->assertSame(OperationNames::DELETE, $observer->getOperation());
        $this->assertSame('', $result[MemberNames::VALUE]);
    }

    /**
     * An empty cell of a default value column that is NOT listed keeps the stored value.
     *
     * @return void
     */
    public function testUnlistedDefaultValueColumnKeepsTheStoredValue()
    {
        $observer = $this->createObserver(array('aup_is_new' => ''), array($this->headers['aup_plain']));

        $result = $observer->merge('aup_is_new', array(MemberNames::VALUE => '0'));

        $this->assertSame(OperationNames::UPDATE, $observer->getOperation());
        $this->assertSame('0', $result[MemberNames::VALUE]);
    }

    /**
     * Without any clean-up column the value of a default value column is kept as well.
     *
     * @return void
     */
    public function testDefaultValueColumnWithoutCleanUpColumnsKeepsTheStoredValue()
    {
        $observer = $this->createObserver(array('aup_is_new' => ''), null);

        $result = $observer->merge('aup_is_new', array(MemberNames::VALUE => '0'));

        $this->assertSame(OperationNames::UPDATE, $observer->getOperation());
        $this->assertSame('0', $result[MemberNames::VALUE]);
    }

    /**
     * A column without a default value is deleted on an empty cell, whatever the clean-up list says.
     *
     * @return void
     */
    public function testColumnWithoutDefaultValueIsDeleted()
    {
        $observer = $this->createObserver(array(), array());

        $result = $observer->merge('aup_plain', array(MemberNames::VALUE => 'x'));

        $this->assertSame(OperationNames::DELETE, $observer->getOperation());
        $this->assertSame('', $result[MemberNames::VALUE]);
    }

    /**
     * A default value column that is not part of the file is not translated and keeps the stored value.
     *
     * @return void
     */
    public function testDefaultValueColumnWithoutHeaderKeepsTheStoredValue()
    {
        $observer = $this->createObserver(array('aup_missing' => ''), array($this->headers['aup_is_new']));

        $result = $observer->merge('aup_missing', array(MemberNames::VALUE => '0'));

        $this->assertSame(OperationNames::UPDATE, $observer->getOperation());
        $this->assertSame('0', $result[MemberNames::VALUE]);
    }
}
