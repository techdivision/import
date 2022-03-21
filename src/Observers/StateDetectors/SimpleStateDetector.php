<?php

/**
 * TechDivision\Import\Observers\StateDetectors\SimpleStateDetector
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers\StateDetectors;

use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Observers\StateDetectorInterface;

/**
 * Simple state detector implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleStateDetector implements StateDetectorInterface
{

    /**
     * Detect's and return's the entity state on the specific entity conditions and return's it.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer      The observer instance to detect the state for
     * @param array                                            $entity        The entity loaded from the database
     * @param array                                            $attr          The entity data from the import file
     * @param string|null                                      $changeSetName The change set name to use
     *
     * @return string The detected entity state
     */
    public function detect(ObserverInterface $observer, array $entity, array $attr, $changeSetName = null)
    {
        return EntityStatus::STATUS_UPDATE;
    }
}
