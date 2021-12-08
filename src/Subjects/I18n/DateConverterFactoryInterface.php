<?php

/**
 * TechDivision\Import\Subjects\I18n\DateConverterFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\I18n;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Interface for date converter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface DateConverterFactoryInterface
{

    /**
     * Creates and returns the date converter instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the date converter for
     *
     * @return \TechDivision\Import\Subjects\I18n\DateConverterInterface The date converter instance
     */
    public function createDateConverter(SubjectConfigurationInterface $subject);
}
