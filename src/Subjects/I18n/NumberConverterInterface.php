<?php

/**
 * TechDivision\Import\Subjects\I18n\NumberConverterInterface
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
interface NumberConverterInterface
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
     * Converts the passed number into a float value.
     *
     * @param string $number The number to parse
     *
     * @return float The float value of the number
     */
    public function convert($number);

    /**
     * Parse a string into a number using the current formatter rules.
     *
     * @param string  $value    The value to be converted
     * @param integer $type     The formatting type to use, by default NumberFormatter::TYPE_DOUBLE is used
     * @param integer $position The offset in the string at which to begin parsing, on return this value will hold the offset at which parsing ended
     *
     * @return float The value of the parsed number or FALSE on error
     */
    public function parse($value, $type = \NumberFormatter::TYPE_DOUBLE, &$position = null);
}
