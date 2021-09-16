<?php

/**
 * TechDivision\Import\Observers\EntityMergers\GenericCompositeEntityMerger
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers\EntityMergers;

use TechDivision\Import\Observers\ObserverInterface;

/**
 * A composite entity merger implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericCompositeEntityMerger extends \ArrayObject implements EntityMergerInterface
{

    /**
     * Merge's and return's the entity with the passed attributes.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer instance to detect the state for
     * @param array                                            $entity   The entity loaded from the database
     * @param array                                            $attr     The entity data from the import file
     *
     * @return array The entity attributes that has to be merged
     * @throws \InvalidArgumentException Is thrown, if one of the elements does not implement the expected interface
     */
    public function merge(ObserverInterface $observer, array $entity, array $attr) : array
    {

        // invoke the entity mergers
        foreach ($this as $key => $entityMerger) {
            if ($entityMerger instanceof EntityMergerInterface) {
                $attr = $entityMerger->merge($observer, $entity, $attr);
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Element #%d doesn\'t implement expected interface "TechDivision\Import\Observers\EntityMergers\EntityMergerInterface"', $key)
                );
            }
        }

        // return the entity attributes that has to be merged
        return $attr;
    }
}
