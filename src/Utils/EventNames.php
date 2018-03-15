<?php

/**
 * TechDivision\Import\Utils\EventKeys
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

namespace TechDivision\Import\Utils;

/**
 * A utility class with the available event names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EventNames
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
     * The event name for the event when an import artefact has successfully been processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_ROW_PROCESS_START = 'subject.artefact.row.process.start';

    /**
     * The event name for the event when an import artefact has successfully been processed.
     *
     * @var string
     */
    const SUBJECT_ARTEFACT_ROW_PROCESS_SUCCESS = 'subject.artefact.row.process.success';
}
