<?php

/**
 * TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface
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

/**
 * The interface for all subject implementations that supports filesystem handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface CleanUpColumnsSubjectInterface
{

    /**
     * Merge the columns from the configuration with all image type columns to define which
     * columns should be cleaned-up.
     *
     * @return array The columns that has to be cleaned-up
     */
    public function getCleanUpColumns();
}
