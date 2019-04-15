<?php

/**
 * TechDivision\Import\Observers\GenericCompositeObserver
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
 * A generic observer implementation that implements the composit pattern to bundle
 * the necessary observers of a special use case to simplify configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericCompositeObserver implements ObserverInterface
{

    /**
     * The composite's subject instance.
     *
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    protected $subject;

    /**
     * The actual row that will be processed by the composite's observers.
     *
     * @var array
     */
    protected $row = array();

    /**
     * The array with the composite's observers.
     *
     * @var \TechDivision\Import\Observers\ObserverInterface[]
     */
    protected $observers = array();

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the subject
        $this->setSubject($subject);

        // process the observers
        foreach ($this->getObservers() as $observer) {
            // query whether or not we have to skip the row
            if ($subject->rowHasToBeSkipped()) {
                // log a debug message with the actual line nr/file information
                $subject->getSystemLogger()->warning(
                    $subject->appendExceptionSuffix(
                        sprintf(
                            'Skip processing operation "%s" after observer "%s"',
                            $subject->getOperationName(),
                            $subject->getConfiguration()->getId()
                        )
                    )
                );

                // skip the row
                break;
            }

            // if not, set the subject and process the observer
            $subject->setRow($observer->handle($subject));
        }

        // returns the subject's row
        return $subject->getRow();
    }

    /**
     * Return's the observer's subject instance.
     *
     * @return \TechDivision\Import\Subjects\SubjectInterface The observer's subject instance
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Adds the passed observer to the composites array with observers.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The observer to add
     *
     * @return void
     */
    public function addObserver(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Set's the obeserver's subject instance to initialize the observer with.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The observer's subject
     *
     * @return void
     */
    protected function setSubject(SubjectInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the array with the composite's observers.
     *
     * @return \TechDivision\Import\Observers\ObserverInterface[]
     */
    protected function getObservers()
    {
        return $this->observers;
    }
}
