<?php

/**
 * TechDivision\Import\Subjects\I18n\DateConverterInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\I18n;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Interface for all date converter implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface DateConverterInterface
{

    /**
     * Sets the subject configuration instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return void
     */
    public function setSubjectConfiguration(SubjectConfigurationInterface $subjectConfiguration);

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getSubjectConfiguration();

    /**
     * Converts the passed date into a Magento 2 compatible date format.
     *
     * @param string $date   The date to convert
     * @param string $format The date format to convert to
     *
     * @return string The converted date
     */
    public function convert($date, $format = 'Y-m-d H:i:s');
}
