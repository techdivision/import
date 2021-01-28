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
use TechDivision\Import\Serializer\SerializerFactoryInterface;

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
class AdditionalAttributeObserver extends AbstractObserver implements ObserverFactoryInterface
{

    /**
     * The serializer used to serializer/unserialize the categories from the path column.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * The serializer factory instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerFactoryInterface
     */
    protected $serializerFactory;

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface $serializerFactory The serializer factory instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null $stateDetector     The state detector instance to use
     */
    public function __construct(
        SerializerFactoryInterface $serializerFactory,
        StateDetectorInterface $stateDetector = null
    ) {

        // initialize the bunch processor and the attribute loader instance
        $this->serializerFactory = $serializerFactory;

        // pass the state detector to the parent method
        parent::__construct($stateDetector);
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // initialize the serializer instance
        $this->serializer = $this->serializerFactory->createSerializer($subject->getConfiguration()->getImportAdapter());

        // return the initialized instance
        return $this;
    }

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
            $additionalAttributes = $this->serializer->explode($additionalAttributes);
            // iterate over the attributes and append them to the row
            foreach ($additionalAttributes as $additionalAttribute) {
                // initialize the option value
                $optionValue = '';
                // explode the attribute code/option value from the attribute
                $exploded = $this->serializer->explode($additionalAttribute, '=');
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
