<?php

/**
 * TechDivision\Import\Subjects\I18n\NumberConverterFactoryInterface
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
 * Interface for number converter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface NumberConverterFactoryInterface
{

    /**
     * Creates and returns the number converter instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the number converter for
     *
     * @return \TechDivision\Import\Subjects\I18n\NumberConverterInterface The number converter instance
     */
    public function createNumberConverter(SubjectConfigurationInterface $subject);
}
