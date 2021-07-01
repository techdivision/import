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

use TechDivision\Import\Observers\ObserverInterface;

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
     * @var \TechDivision\Import\Observers\ObserverInterface
     */
    protected $observer;

    /**
     * Set's the callback's observer instance.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The callback's observer
     *
     * @return void
     */
    protected function setObserver(ObserverInterface $observer)
    {
        $this->observer = $observer;
    }

    /**
     * Return's the callback's observer instance.
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The callback's observer
     */
    protected function getObserver()
    {
        return $this->observer;
    }

    /**
     * Return's the observer's subject instance.
     *
     * @return \TechDivision\Import\Subjects\SubjectInterface The observer's subject instance
     */
    protected function getSubject()
    {
        return $this->getObserver()->getSubject();
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
     * Returns the configured entity type code.
     *
     * @return string The entity type code from the configuration
     */
    protected function getEntityTypeCode()
    {
        return $this->getSubject()->getEntityTypeCode();
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
     * Queries whether or not strict mode is enabled or not, default is True.
     * Backward compatibility
     * debug = true strict = true -> isStrict == FALSE
     * debug = true strict = false -> isStrict == FALSE
     * debug = false strict = true -> isStrict == TRUE
     * debug = false strict = false -> isStrict == FALSE
     *
     * @return boolean TRUE if strict mode is enabled and debug mode disable, else FALSE
     */
    public function isStrictMode()
    {
        return $this->getSubject()->isStrictMode();
    }

    /**
     * Raises the value for the counter with the passed key by one.
     *
     * @param mixed $counterName The name of the counter to raise
     *
     * @return integer The counter's new value
     */
    protected function raiseCounter($counterName)
    {
        return $this->getSubject()->raiseCounter($counterName);
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
        $this->getSubject()->mergeAttributesRecursive($status);
    }

    /**
     * Resolve's the value with the passed colum name from the actual row. If a callback will
     * be passed, the callback will be invoked with the found value as parameter. If
     * the value is NULL or empty, the default value will be returned.
     *
     * @param string        $name     The name of the column to return the value for
     * @param mixed|null    $default  The default value, that has to be returned, if the row's value is empty
     * @param callable|null $callback The callback that has to be invoked on the value, e. g. to format it
     *
     * @return mixed|null The, almost formatted, value
     */
    protected function getValue($name, $default = null, callable $callback = null)
    {
        return $this->getSubject()->getValue($name, $default, $callback);
    }

    /**
     * Return's the store ID of the store with the passed store view code
     *
     * @param string $storeViewCode The store view code to return the store ID for
     *
     * @return integer The ID of the store with the passed ID
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    protected function getStoreId($storeViewCode)
    {
        return $this->getSubject()->getStoreId($storeViewCode);
    }

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    protected function getRowStoreId($default = null)
    {
        return $this->getSubject()->getRowStoreId($default);
    }

    /**
     * Return's the unique identifier of the actual row, e. g. a products SKU.
     *
     * @return mixed The row's unique identifier
     */
    abstract protected function getUniqueIdentifier();
}
