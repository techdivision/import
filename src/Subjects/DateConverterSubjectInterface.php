<?php

/**
 * TechDivision\Import\Subjects\DateConverterSubjectInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Subjects\I18n\DateConverterInterface;

/**
 * The interface for all subjects that provides date converting functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface DateConverterSubjectInterface
{

    /**
     * Sets the date converter instance.
     *
     * @param \TechDivision\Import\Subjects\I18n\DateConverterInterface $dateConverter The date converter instance
     *
     * @return void
     */
    public function setDateConverter(DateConverterInterface $dateConverter);

    /**
     * Returns the date converter instance.
     *
     * @return \TechDivision\Import\Subjects\I18n\DateConverterInterface The date converter instance
     */
    public function getDateConverter();
}
