<?php

/**
 * TechDivision\Import\Observers\AdditionalAttributeObserver
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Observer that prepares the additional product attribues found in the CSV file
 * in the row 'additional_attributes'.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeObserver extends AbstractObserver
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not the row has additional attributes
        if ($additionalAttributes = $this->getValue(ColumnKeys::ADDITIONAL_ATTRIBUTES)) {
            // load the subject instance
            $subject = $this->getSubject();
            // explode the additional attributes
            $additionalAttributes = $subject->explode($additionalAttributes, $this->getMultipleFieldDelimiter());
            // iterate over the attributes and append them to the row
            foreach ($additionalAttributes as $additionalAttribute) {
                // initialize the option value
                $optionValue = '';
                // explode the attribute code/option value from the attribute
                $exploded = $subject->explode($additionalAttribute, '=');
                // initialize attribute code and option value, depending on what we've exploded
                if (sizeof($exploded) < 1) {
                    continue;
                } elseif (sizeof($exploded) === 1) {
                    list ($attributeCode) = $exploded;
                } else {
                    list ($attributeCode, $optionValue) = $exploded;
                }

                // try to load the appropriate key for the value
                if ($subject->hasHeader($attributeCode) === false) {
                    $subject->addHeader($attributeCode);
                }

                // append/replace the attribute value
                $this->setValue($attributeCode, $optionValue);

                // add a log message in debug mod
                if ($subject->isDebugMode()) {
                    $subject->getSystemLogger()->debug(
                        sprintf(
                            'Extract new column "%s" with value "%s" from column "%s" in file %s on line %d',
                            $attributeCode,
                            $optionValue,
                            ColumnKeys::ADDITIONAL_ATTRIBUTES,
                            $subject->getFilename(),
                            $subject->getLineNumber()
                        )
                    );
                }
            }
        }
    }
}
