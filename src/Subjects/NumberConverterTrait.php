<?php

/**
 * TechDivision\Import\Subjects\NumberConverterTrait
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Subjects\I18n\NumberConverterInterface;

/**
 * The trait implementation that provides number convertering functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait NumberConverterTrait
{

    /**
     * Return the number converter.
     *
     * @return \TechDivision\Import\Subjects\I18n\NumberConverterInterface
     */
    protected $numberConverter;

    /**
     * Sets the number converter instance.
     *
     * @param \TechDivision\Import\Subjects\I18n\NumberConverterInterface $numberConverter The number converter instance
     *
     * @return void
     */
    public function setNumberConverter(NumberConverterInterface $numberConverter)
    {
        $this->numberConverter = $numberConverter;
    }

    /**
     * Returns the number converter instance.
     *
     * @return \TechDivision\Import\Subjects\I18n\NumberConverterInterface The number converter instance
     */
    public function getNumberConverter()
    {
        return $this->numberConverter;
    }
}
