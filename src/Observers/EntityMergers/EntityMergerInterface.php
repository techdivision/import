<?php

/**
 * TechDivision\Import\Observers\EntityMergers\EntityMergerInterface
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

namespace TechDivision\Import\Observers\EntityMergers;

use TechDivision\Import\Observers\ObserverInterface;

/**
 * Interface for entity merger implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EntityMergerInterface
{

    /**
     * Merge's and return's the entity with the passed attributes.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer instance to detect the state for
     * @param array                                            $entity   The entity loaded from the database
     * @param array                                            $attr     The entity data from the import file
     *
     * @return array The entity attributes that has to be merged
     */
    public function merge(ObserverInterface $observer, array $entity, array $attr) : array;
}
