<?php

/**
 * TechDivision\Import\Observers\AbstractAttributeObserver
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

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Observer that creates/updates the EAV attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractAttributeObserver extends AbstractObserver implements AttributeCodeAndValueAwareObserverInterface
{

    /**
     * The trait that provides the functionality to persist the attributes found in the CSV file.
     *
     * @var \TechDivision\Import\Observers\AttributeObserverTrait
     */
    use AttributeObserverTrait;

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
     * Persist's the passed varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistVarcharAttribute($attribute);

    /**
     * Persist's the passed integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistIntAttribute($attribute);

    /**
     * Persist's the passed decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistDecimalAttribute($attribute);

    /**
     * Persist's the passed datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistDatetimeAttribute($attribute);

    /**
     * Persist's the passed text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    abstract protected function persistTextAttribute($attribute);
}
