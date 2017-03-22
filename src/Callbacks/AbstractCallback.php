<?php

/**
 * TechDivision\Import\Callbacks\AbstractCallback
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

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * An abstract callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractCallback implements CallbackInterface
{

    /**
     * The observer's subject instance.
     *
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    protected $subject;

    /**
     * Initializes the observer with the passed subject instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface|null $subject The observer's subject instance
     */
    public function __construct(SubjectInterface $subject = null)
    {
        if ($subject != null) {
            $this->setSubject($subject);
        }
    }

    /**
     * Set's the obeserver's subject instance to initialize the observer with.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The observer's subject
     *
     * @return void
     */
    public function setSubject(SubjectInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Return's the observer's subject instance.
     *
     * @return \TechDivision\Import\Subjects\SubjectInterface The observer's subject instance
     */
    protected function getSubject()
    {
        return $this->subject;
    }

    /**
     * Return's the system logger.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    protected function getSystemLogger()
    {
        return $this->getSubject()->getSystemLogger();
    }

    /**
     * Append's the exception suffix containing filename and line number to the
     * passed message. If no message has been passed, only the suffix will be
     * returned.
     *
     * @param string|null $message    The message to append the exception suffix to
     * @param string|null $filename   The filename used to create the suffix
     * @param string|null $lineNumber The line number used to create the suffx
     *
     * @return string The message with the appended exception suffix
     */
    protected function appendExceptionSuffix($message = null, $filename = null, $lineNumber = null)
    {
        return $this->getSubject()-> appendExceptionSuffix($message, $filename, $lineNumber);
    }

    /**
     * Wraps the passed exeception into a new one by trying to resolve the original filname,
     * line number and column name and use it for a detailed exception message.
     *
     * @param string     $columnName The column name that should be resolved
     * @param \Exception $parent     The exception we want to wrap
     * @param string     $className  The class name of the exception type we want to wrap the parent one
     *
     * @return \Exception the wrapped exception
     */
    protected function wrapException(
        $columnName,
        \Exception $parent = null,
        $className = '\TechDivision\Import\Exceptions\WrappedColumnException'
    ) {
        return $this->getSubject()->wrapException($columnName, $parent, $className);
    }

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    protected function isDebugMode()
    {
        return $this->getSubject()->isDebugMode();
    }

    /**
     * Merge the passed array into the status of the actual import.
     *
     * @param array $status The status information to be merged
     *
     * @return void
     */
    protected function mergeAttributesRecursive(array $status)
    {

        // load the subject instance
        $subject = $this->getSubject();

        // merge the passed status
        $subject->getRegistryProcessor()->mergeAttributesRecursive(
            $subject->getSerial(),
            $status
        );
    }
}
