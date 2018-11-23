<?php

/**
 * TechDivision\Import\Plugins\SubjectExecutor
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Callbacks\CallbackVisitorInterface;
use TechDivision\Import\Observers\ObserverVisitorInterface;
use TechDivision\Import\Subjects\SubjectFactoryInterface;
use TechDivision\Import\Subjects\ExportableSubjectInterface;
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
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Callbacks\CallbackVisitorInterface $callbackVisitor The callback visitor instance
     * @param \TechDivision\Import\Observers\ObserverVisitorInterface $observerVisitor The observer visitor instance
     * @param \TechDivision\Import\Subjects\SubjectFactoryInterface   $subjectFactory  The subject factory instance
     */
    public function __construct(
        CallbackVisitorInterface $callbackVisitor,
        ObserverVisitorInterface $observerVisitor,
        SubjectFactoryInterface $subjectFactory
    ) {

        // initialize the callback/observer visitors
        $this->callbackVisitor = $callbackVisitor;
        $this->observerVisitor = $observerVisitor;
        $this->subjectFactory = $subjectFactory;
    }

    /**
     * Executes the passed subject.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject  The message with the subject information
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
            // setup the subject instance
            $subjectInstance->setUp($serial);

            // initialize the callbacks/observers
            $this->callbackVisitor->visit($subjectInstance);
            $this->observerVisitor->visit($subjectInstance);

            // finally import the CSV file
            $subjectInstance->import($serial, $pathname);

            // query whether or not, we've to export artefacts
            if ($subjectInstance instanceof ExportableSubjectInterface) {
                $subjectInstance->export(
                    $matches[BunchKeys::FILENAME],
                    $matches[BunchKeys::COUNTER]
                );
            }

            // tear down the subject instance
            $subjectInstance->tearDown($serial);
        } catch (\Exception $e) {
            // query whether or not, we've to export artefacts
            if ($subjectInstance instanceof ExportableSubjectInterface) {
                // tear down the subject instance
                $subjectInstance->tearDown($serial);
            }

            // re-throw the exception
            throw $e;
        }
    }
}
