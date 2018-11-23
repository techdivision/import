<?php

/**
 * TechDivision\Import\Subjects\I18n\NumberConverter
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
 * Simple number converter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleNumberConverter implements NumberConverterInterface
{

    /**
     * The subject configuraiton instance.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * The target number formatter.
     *
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * Initialize the number converter instance.
     */
    public function __construct()
    {
        $this->formatter = \NumberFormatter::create('en_US', \NumberFormatter::DECIMAL);
    }

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
     * Returns the number converter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\NumberConverterConfigurationInterface The number converter configuration
     */
    protected function getNumberConverterConfiguration()
    {
        return $this->getSubjectConfiguration()->getNumberConverter();
    }

    /**
     * Returns the target number formatter instance.
     *
     * @return \NumberFormatter The target number formatter instance
     */
    protected function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Converts the passed number into a float value.
     *
     * @param string $number The number to parse
     *
     * @return float The float value of the number
     */
    public function convert($number)
    {

        // initialize the formatter instance with the source locale
        $formatter = \NumberFormatter::create($this->getNumberConverterConfiguration()->getLocale(), \NumberFormatter::DECIMAL);

        // parse, format and return the value
        return $this->getFormatter()->format($formatter->parse($number));
    }
}
