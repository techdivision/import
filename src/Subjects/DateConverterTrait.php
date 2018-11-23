<?php

/**
 * TechDivision\Import\Subjects\DateConverterTrait
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

use TechDivision\Import\Subjects\I18n\DateConverterInterface;

/**
 * The trait implementation that provides date convertering functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait DateConverterTrait
{

    /**
     * Return the number converter.
     *
     * @return \TechDivision\Import\Subjects\I18n\DateConverterInterface
     */
    protected $dateConverter;

    /**
     * Sets the date converter instance.
     *
     * @param \TechDivision\Import\Subjects\I18n\DateConverterInterface $dateConverter The date converter instance
     *
     * @return void
     */
    public function setDateConverter(DateConverterInterface $dateConverter)
    {
        $this->dateConverter = $dateConverter;
    }

    /**
     * Returns the date converter instance.
     *
     * @return \TechDivision\Import\Subjects\I18n\DateConverterInterface The date converter instance
     */
    public function getDateConverter()
    {
        return $this->dateConverter;
    }
}
