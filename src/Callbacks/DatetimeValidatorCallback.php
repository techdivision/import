<?php

/**
 * TechDivision\Import\Callbacks\DatetimeValidatorCallback
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * A callback implementation that validates the value is a valid date.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class DatetimeValidatorCallback implements CallbackInterface, CallbackFactoryInterface
{

    /**
     * The flag to query whether or not the value can be empty.
     *
     * @var boolean
     */
    protected $nullable = false;

    /**
     * The default source date format.
     *
     * @var string
     */
    protected $sourceDateFormat = 'n/d/y, g:i A';

    /**
     * Initializes the callback with the loader instance.
     *
     * @param boolean $nullable The flag to decide whether or not the value can be empty
     */
    public function __construct($nullable = false)
    {
        $this->nullable = $nullable;
    }

    /**
     * Will be invoked by the callback visitor when a factory has been defined to create the callback instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Callbacks\CallbackInterface The callback instance
     */
    public function createCallback(SubjectInterface $subject)
    {

        // load the source date format from the subject's date converter configuration
        $this->sourceDateFormat = $subject->getSourceDateFormat();

        // return the initialized instance
        return $this;
    }

    /**
     * Query whether or not the passed value IS empty and empty values are allowed.
     *
     * @param string $attributeValue The attribute value to query for
     *
     * @return boolean TRUE if empty values are allowed and the passed value IS empty
     */
    protected function isNullable($attributeValue)
    {
        return $this->nullable && ($attributeValue === '' || $attributeValue === null);
    }

    /**
     * Query whether or not the passed value IS a valid date.
     *
     * @param string $attributeValue The attribute value to query for
     *
     * @return boolean TRUE if the passed value IS a valid date
     */
    protected function isDate($attributeValue)
    {
        return \DateTime::createFromFormat($this->sourceDateFormat, $attributeValue);
    }

    /**
     * Will be invoked by the observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {

        // query whether or not we've found a value and it is a valid date
        if ($this->isNullable($attributeValue) || $this->isDate($attributeValue)) {
            return;
        }

        // throw an exception if the value is NOT in the array
        throw new \InvalidArgumentException(
            sprintf(
                'Found invalid date "%s" for column "%s"',
                $attributeValue,
                $attributeCode
            )
        );
    }
}
