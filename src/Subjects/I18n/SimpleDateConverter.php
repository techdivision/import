<?php

/**
 * TechDivision\Import\Subjects\I18n\SimpleDateConverter
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\I18n;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Simple date converter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleDateConverter implements DateConverterInterface
{

    /**
     * The subject configuraiton instance.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * Sets the subject configuration instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return void
     */
    public function setSubjectConfiguration(SubjectConfigurationInterface $subjectConfiguration)
    {
        $this->subjectConfiguration = $subjectConfiguration;
    }

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getSubjectConfiguration()
    {
        return $this->subjectConfiguration;
    }

    /**
     * Returns the date converter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\DateConverterConfigurationInterface The date converter configuration
     */
    protected function getDateConverterConfiguration()
    {
        return $this->getSubjectConfiguration()->getDateConverter();
    }

    /**
     * Converts the passed date into a Magento 2 compatible date format.
     *
     * @param string $date   The date to convert
     * @param string $format The date format to convert to
     *
     * @return string The converted date
     */
    public function convert($date, $format = 'Y-m-d H:i:s')
    {
        // create a DateTime instance from the passed value
        if ($dateTime = \DateTime::createFromFormat($this->getDateConverterConfiguration()->getSourceDateFormat(), $date)) {
            return $dateTime->format($format);
        }

        // Date is not in configured format? Try if is a default format
        try {
            return (new \DateTime($date))->format($format);
        } catch( \Exception $e) {
            // Catch if $date is no default date format
        }

        // return NULL, if the passed value is NOT a valid date
        return null;
    }
}
