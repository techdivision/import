<?php

/**
 * TechDivision\Import\Observers\AttributeLoaderInterface
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

namespace TechDivision\Import\Observers;

/**
 * Interface for all dynamic attribute loader implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface AttributeLoaderInterface
{

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @param \TechDivision\Import\Observers\DynamicAttributeObserverInterface $observer The observer to load the attributes for
     * @param array                                                            $columns  The array with the possible columns (column name as key and backend type as value) to load the data from
     *
     * @return array The prepared attributes
     * @throws \Exception Is thrown, if the size of the option values doesn't equals the size of swatch values, in case
     */
    public function load(DynamicAttributeObserverInterface $observer, array $columns);
}
