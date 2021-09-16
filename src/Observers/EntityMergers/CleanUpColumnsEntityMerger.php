<?php

/**
 * TechDivision\Import\Observers\EntityMergers\CleanUpColumnsEntityMerger
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

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface;
use TechDivision\Import\Utils\EntityStatus;

/**
 * An entity merge implementation that is aware of cleaning-up attributes, if NOT in the
 * array with the columns that has to be cleaned-up, that has no value in it's columns.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CleanUpColumnsEntityMerger implements EntityMergerInterface
{

    /**
     * Array with virtual column name mappings (this is a temporary
     * solution till techdivision/import#179 as been implemented).
     *
     * @var array
     * @todo https://github.com/techdivision/import/issues/179
     */
    private $reverseHeaderMappings = array();

    /**
     * Initializes the merger with the virtual column mapping
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface|null $haederMappingLoader The loader for the virtual mappings
     */
    public function __construct(LoaderInterface $haederMappingLoader = null)
    {
        $this->reverseHeaderMappings = array_merge(
            $this->reverseHeaderMappings,
            $haederMappingLoader ? array_flip($haederMappingLoader->load()) : array()
        );
    }

    /**
     * Merge's and return's the entity with the passed attributes.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer instance to detect the state for
     * @param array                                            $entity   The entity loaded from the database
     * @param array                                            $attr     The entity data from the import file
     *
     * @return array The entity attributes that has to be merged
     */
    public function merge(ObserverInterface $observer, array $entity, array $attr) : array
    {

        // query whether or not the subject has columns that has to be cleaned-up
        if (($subject = $observer->getSubject()) instanceof CleanUpColumnsSubjectInterface) {
            // load the columns that has to be cleaned-up
            $cleanUpColumns =  $subject->getCleanUpColumns();
            // load the column/member names from the attributes
            $columnNames = array_keys($attr);

            // iterate over the column names
            foreach ($columnNames as $columnName) {
                // we do NOT clean-up column names that has to be cleaned-up
                if ($observer->hasValue(isset($this->reverseHeaderMappings[$columnName]) ? $this->reverseHeaderMappings[$columnName] : $columnName) ||
                    in_array($columnName, $cleanUpColumns) ||
                    $columnName === EntityStatus::MEMBER_NAME
                ) {
                    continue;
                }
                // unset the column, because it has NOT been cleaned-up
                unset($attr[$columnName]);
            }
        }

        // return the processed attributes
        return $attr;
    }
}
