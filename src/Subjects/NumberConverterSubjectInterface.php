<?php

/**
 * TechDivision\Import\Subjects\NumberConverterSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Subjects\I18n\NumberConverterInterface;

/**
 * The interface for all subjects that provides number converting functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface NumberConverterSubjectInterface
{

    /**
     * Sets the number converter instance.
     *
     * @param \TechDivision\Import\Subjects\I18n\NumberConverterInterface $numberConverter The number converter instance
     *
     * @return void
     */
    public function setNumberConverter(NumberConverterInterface $numberConverter);

    /**
     * Returns the number converter instance.
     *
     * @return \TechDivision\Import\Subjects\I18n\NumberConverterInterface The number converter instance
     */
    public function getNumberConverter();
}
