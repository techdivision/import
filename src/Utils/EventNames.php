<?php

/**
 * TechDivision\Import\Utils\EventNames
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * A utility class with the available event names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EventNames extends \TechDivision\Import\Dbal\Utils\EventNames
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * The event name for the event when the application will be setted up.
     *
     * @var string
     */
    const APP_SET_UP = 'app.set.up';

    /**
     * The event name for the event when the application will be teared down.
     *
     * @var string
     */
    const APP_TEAR_DOWN = 'app.tear.down';

    /**
     * The event name for the event before the application start's the transaction (if single transaction mode has been activated).
     *
     * @var string
     */
    const APP_PROCESS_TRANSACTION_START = 'app.process.transaction.start';

    /**
     * The event name for the event after the application has the transaction committed successfully (if single transaction mode has been activated).
     *
     * @var string
     */
    const APP_PROCESS_TRANSACTION_SUCCESS = 'app.process.transaction.success';

    /**
     * The event name for the event after the application rollbacked the transaction (if single transaction mode has been activated).
     *
     * @var string
     */
    const APP_PROCESS_TRANSACTION_FAILURE = 'app.process.transaction.failure';

    /**
     * The event name for the event after the application has the transaction finished (independ if it has failed or succeeded).
     *
     * @var string
     */
    const APP_PROCESS_TRANSACTION_FINISHED = 'app.process.transaction.finished';

    /**
     * The event name for the event that has to be fired before the plugin will be executed.
     *
     * @var string
     */
    const PLUGIN_PROCESS_START = 'plugin.process.start';

    /**
     * The event name for the event that has to be fired after the plugin has been executed.
     *
     * @var string
     */
    const PLUGIN_PROCESS_SUCCESS = 'plugin.process.success';

    /**
     * The event name for the event that has to be fired when the plugin throws an exception.
     *
     * @var string
     */
    const PLUGIN_PROCESS_FAILURE = 'plugin.process.failure';

    /**
     * The event name for the event that has to be fired before the plugin's export method will be invoked.
     *
     * @var string
     */
    const PLUGIN_EXPORT_START = 'plugin.export.start';

    /**
     * The event name for the event that has to be fired after the plugin's export method has been invoked.
     *
     * @var string
     */
    const PLUGIN_EXPORT_SUCCESS = 'plugin.export.success';

    /**
     * The event name for invoke the event that has to be fired when the plugin's export method throws an exception.
     *
     * @var string
     */
    const PLUGIN_EXPORT_FAILURE = 'plugin.export.failure';

    /**
     * The event name for the event that has to be fired before the subject's import method will be invoked.
     *
     * @var string
     */
    const SUBJECT_IMPORT_START = 'subject.import.start';

    /**
     * The event name for the event that has to be fired after the subject's import method has been invoked.
     *
     * @var string
     */
    const SUBJECT_IMPORT_SUCCESS = 'subject.import.success';

    /**
     * The event name for invoke the event that has to be fired when the subject's import method throws an exception.
     *
     * @var string
     */
    const SUBJECT_IMPORT_FAILURE = 'subject.import.failure';

    /**
     * The event name for the event that has to be fired before the subject's export method will be invoked.
     *
     * @var string
     */
    const SUBJECT_EXPORT_START = 'subject.export.start';

    /**
     * The event name for the event that has to be fired after the subject's export method has been invoked.
     *
     * @var string
     */
    const SUBJECT_EXPORT_SUCCESS = 'subject.export.success';

    /**
     * The event name for invoke the event that has to be fired when the subject's export method throws an exception.
     *
     * @var string
     */
    const SUBJECT_EXPORT_FAILURE = 'subject.export.failure';

    /**
     * The event name for the event before an import artefact will be processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_PROCESS_START = 'subject.artefact.process.start';

    /**
     * The event name for the event when an import artefact has successfully been processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_PROCESS_SUCCESS = 'subject.artefact.process.success';

    /**
     * The event name for the event when an import artefact can not be processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_PROCESS_FAILURE = 'subject.artefact.process.failure';

    /**
     * The event name for the event before a row of the import artefact will be processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_ROW_PROCESS_START = 'subject.artefact.row.process.start';

    /**
     * The event name for the event when an row of the artefact has successfully been processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_ROW_PROCESS_SUCCESS = 'subject.artefact.row.process.success';

    /**
     * The event name for the event before the header row of the import artefact will be processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_START = 'subject.artefact.header.row.process.start';

    /**
     * The event name for the event when the header row of the artefact has successfully been processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_HEADER_ROW_PROCESS_SUCCESS = 'subject.artefact.header.row.process.success';
}
