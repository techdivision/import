<?php

/**
 * TechDivision\Import\Observers\StateDetectors\SimpleStateDetector
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers\StateDetectors;

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Observers\ObserverInterface;
use TechDivision\Import\Observers\StateDetectorInterface;

/**
 * Simple state detector implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleStateDetector implements StateDetectorInterface
{

    /**
     * Detect's and return's the entity state on the specific entity conditions and return's it.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer instance to detect the state for
     * @param array                                            $entity   The entity loaded from the database
     * @param array                                            $attr     The entity data from the import file
     *
     * @return string The detected entity state
     */
    public function detect(ObserverInterface $observer, array $entity, array $attr)
    {
        return EntityStatus::STATUS_UPDATE;
    }
}
