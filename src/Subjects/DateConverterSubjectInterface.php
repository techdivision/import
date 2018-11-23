<?php

/**
 * TechDivision\Import\Subjects\DateConverterSubjectInterface
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
