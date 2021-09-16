<?php

/**
 * TechDivision\Import\Subjects\I18n\NumberConverter
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
 * Simple number converter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
        return $this->getFormatter()->format($this->parse($number));
    }

    /**
     * Parse a string into a number using the current formatter rules.
     *
     * @param string  $value    The value to be converted
     * @param integer $type     The formatting type to use, by default NumberFormatter::TYPE_DOUBLE is used
     * @param integer $position The offset in the string at which to begin parsing, on return this value will hold the offset at which parsing ended
     *
     * @return float The value of the parsed number or FALSE on error
     */
    public function parse($value, $type = \NumberFormatter::TYPE_DOUBLE, &$position = null)
    {
        return \NumberFormatter::create($this->getNumberConverterConfiguration()->getLocale(), \NumberFormatter::DECIMAL)->parse($value, $type, $position);
    }
}
