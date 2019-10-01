<?php

/**
 * TechDivision\Import\Subjects\SubjectExecutor
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

use League\Event\EmitterInterface;
use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Utils\EventNames;
use TechDivision\Import\Callbacks\CallbackVisitorInterface;
use TechDivision\Import\Observers\ObserverVisitorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * The subject executor instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectExecutor implements SubjectExecutorInterface
{

    /**
     * The subject factory instance.
     *
     * @var \TechDivision\Import\Observers\ObserverVisitorInterface
     */
    protected $observerVisitor;

    /**
     * The subject factory instance.
     *
     * @var \TechDivision\Import\Callbacks\CallbackVisitorInterface
     */
    protected $callbackVisitor;

    /**
     * The subject factory instance.
     *
     * @var \TechDivision\Import\Subjects\SubjectFactoryInterface
     */
    protected $subjectFactory;

    /**
     * The event emitter instance.
     *
     * @var \League\Event\EmitterInterface
     */
    protected $emitter;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Callbacks\CallbackVisitorInterface $callbackVisitor The callback visitor instance
     * @param \TechDivision\Import\Observers\ObserverVisitorInterface $observerVisitor The observer visitor instance
     * @param \TechDivision\Import\Subjects\SubjectFactoryInterface   $subjectFactory  The subject factory instance
     * @param \League\Event\EmitterInterface                          $emitter         The event emitter instance
     */
    public function __construct(
        CallbackVisitorInterface $callbackVisitor,
        ObserverVisitorInterface $observerVisitor,
        SubjectFactoryInterface $subjectFactory,
        EmitterInterface $emitter
    ) {

        // initialize the callback/observer visitors
        $this->callbackVisitor = $callbackVisitor;
        $this->observerVisitor = $observerVisitor;
        $this->subjectFactory = $subjectFactory;
        $this->emitter = $emitter;
    }

    /**
     * Executes the passed subject.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject  The subject configuration instance
     * @param array                                                            $matches  The bunch matches
     * @param string                                                           $serial   The UUID of the actual import
     * @param string                                                           $pathname The path to the file to import
     *
     * @return void
     */
    public function execute(SubjectConfigurationInterface $subject, array $matches, $serial, $pathname)
    {

        // initialize the subject and import the bunch
        $subjectInstance = $this->subjectFactory->createSubject($subject);

        try {
            // load the subject + plug-in ID to prepare the event names
            $subjectName = $subject->getName();
            $pluginName = $subject->getPluginConfiguration()->getName();

            // invoke the event that has to be fired before the subject's import method will be invoked
            $this->emitter->emit(EventNames::SUBJECT_IMPORT_START, $subjectInstance);
            $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_IMPORT_START), $subjectInstance);

            // setup the subject instance
            $subjectInstance->setUp($serial);

            // initialize the callbacks/observers
            $this->callbackVisitor->visit($subjectInstance);
            $this->observerVisitor->visit($subjectInstance);

            // finally import the CSV file
            $subjectInstance->import($serial, $pathname);

            // query whether or not, we've to export artefacts
            if ($subjectInstance instanceof ExportableSubjectInterface) {
                try {
                    // invoke the event that has to be fired before the subject's export method will be invoked
                    $this->emitter->emit(EventNames::SUBJECT_EXPORT_START, $subjectInstance);
                    $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_EXPORT_START), $subjectInstance);

                    // export the artefacts if available
                    $subjectInstance->export(
                        $matches[BunchKeys::FILENAME],
                        $matches[BunchKeys::COUNTER]
                    );

                    // invoke the event that has to be fired after the subject's export method has been invoked
                    $this->emitter->emit(EventNames::SUBJECT_EXPORT_SUCCESS, $subjectInstance);
                    $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_EXPORT_SUCCESS), $subjectInstance);
                } catch (\Exception $e) {
                    // invoke the event that has to be fired when the subject's export method throws an exception
                    $this->emitter->emit(EventNames::SUBJECT_EXPORT_FAILURE, $subjectInstance);
                    $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_EXPORT_FAILURE), $subjectInstance);

                    // re-throw the exception
                    throw $e;
                }
            }

            // tear down the subject instance
            $subjectInstance->tearDown($serial);

            // invoke the event that has to be fired after the subject's import method has been invoked
            $this->emitter->emit(EventNames::SUBJECT_IMPORT_SUCCESS, $subjectInstance);
            $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_IMPORT_SUCCESS), $subjectInstance);
        } catch (\Exception $e) {
            // query whether or not, we've to export artefacts
            if ($subjectInstance instanceof ExportableSubjectInterface) {
                // tear down the subject instance
                $subjectInstance->tearDown($serial);
            }

            // invoke the event that has to be fired when the subject's import method throws an exception
            $this->emitter->emit(EventNames::SUBJECT_IMPORT_FAILURE, $subjectInstance);
            $this->emitter->emit(sprintf('%s.%s.%s', $pluginName, $subjectName, EventNames::SUBJECT_IMPORT_FAILURE), $subjectInstance);

            // re-throw the exception
            throw $e;
        }
    }
}
