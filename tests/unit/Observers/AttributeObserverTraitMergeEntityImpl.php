<?php

/**
 * TechDivision\Import\Observers\AttributeObserverTraitMergeEntityImpl
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

/**
 * Observer double that makes mergeEntity() of the attribute observer trait callable.
 *
 * @author    TechDivision GmbH <info@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AttributeObserverTraitMergeEntityImpl extends AbstractAttributeObserver
{

    /**
     * The header positions by column name.
     *
     * @var array
     */
    private $headers;

    /**
     * Initializes the double.
     *
     * @param array      $headers             The header positions by column name
     * @param array      $defaultColumnValues The columns with a default value
     * @param array|null $cleanUpColumnKeys   The header positions of the columns to clean up
     */
    public function __construct(array $headers, array $defaultColumnValues, ?array $cleanUpColumnKeys)
    {
        $this->headers = $headers;
        $this->defaultColumnValues = $defaultColumnValues;
        $this->cleanUpEmptyColumnKeys = $cleanUpColumnKeys;
    }

    /**
     * Merges an empty attribute value into an existing entity.
     *
     * @param string $attributeCode The attribute code
     * @param array  $entity        The stored entity
     *
     * @return array The merged entity
     */
    public function merge($attributeCode, array $entity)
    {
        $this->attributeCode = $attributeCode;
        $this->attributeValue = '';
        return $this->mergeEntity($entity, array(\TechDivision\Import\Utils\MemberNames::VALUE => ''));
    }

    /**
     * Returns the operation the last merge has chosen.
     *
     * @return string The operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    /**
     * {@inheritDoc}
     */
    protected function persistVarcharAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function persistIntAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function persistDecimalAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function persistDatetimeAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function persistTextAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    protected function getPrimaryKey()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPrimaryKeyMemberName()
    {
        return 'entity_id';
    }

    /**
     * {@inheritDoc}
     */
    protected function getPrimaryKeyColumnName()
    {
        return 'sku';
    }

    /**
     * {@inheritDoc}
     */
    protected function storeViewHasBeenProcessed($pk, $storeViewCode)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function deleteDatetimeAttribute(array $row, $name = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function deleteDecimalAttribute(array $row, $name = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function deleteIntAttribute(array $row, $name = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function deleteTextAttribute(array $row, $name = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function deleteVarcharAttribute(array $row, $name = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function hasChanges(array $entity)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function hasValue($name)
    {
        return false;
    }
}
