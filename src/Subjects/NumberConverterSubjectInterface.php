<?php

/**
 * TechDivision\Import\Subjects\NumberConverterSubjectInterface
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Subjects\I18n\NumberConverterInterface;

/**
 * The interface for all subjects that provides number converting functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
