<?php

/**
 * TechDivision\Import\Callbacks\CallbackFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * interface for all callback factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CallbackFactoryInterface
{

    /**
     * Will be invoked by the callback visitor when a factory has been defined to create the callback instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Callbacks\CallbackInterface The callback instance
     */
    public function createCallback(SubjectInterface $subject);
}
