<?php

/**
 * TechDivision\Import\Observers\AbstractObserver
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

use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\EntityStatus;

/**
 * An abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractObserver implements ObserverInterface
{

    /**
     * The actual row, that has to be processed.
     *
     * @var array
     */
    protected $row = array();

    /**
     * The obeserver's subject instance.
     *
     * @var object
     */
    protected $subject;

    /**
     * Initializes the observer with the passed subject instance.
     *
     * @param object|null $subject The observer's subject instance
     */
    public function __construct($subject = null)
    {
        if ($subject != null) {
            $this->setSubject($subject);
        }
    }

    /**
     * Set's the obeserver's subject instance to initialize the observer with.
     *
     * @param object $subject The observer's subject
     *
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Return's the observer's subject instance.
     *
     * @return object The observer's subject instance
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->getSubject()->setHeaders($headers);
    }

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     */
    public function getHeaders()
    {
        return $this->getSubject()->getHeaders();
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    public function getRegistryProcessor()
    {
        return $this->getSubject()->getRegistryProcessor();
    }

    /**
     * Set's the actual row, that has to be processed.
     *
     * @param array $row The row
     *
     * @return void
     */
    protected function setRow(array $row)
    {
        $this->row = $row;
    }

    /**
     * Return's the actual row, that has to be processed.
     *
     * @return array The row
     */
    protected function getRow()
    {
        return $this->row;
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
     * Stop's observer execution on the actual row.
     *
     * @return void
     */
    protected function skipRow()
    {
        $this->getSubject()->skipRow();
    }

    /**
     * Return's the name of the file to import.
     *
     * @return string The filename
     */
    protected function getFilename()
    {
        return $this->getSubject()->getFilename();
    }

    /**
     * Return's the actual line number.
     *
     * @return integer The line number
     */
    protected function getLineNumber()
    {
        return $this->getSubject()->getLineNumber();
    }

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    protected function getSystemLogger($name = LoggerKeys::SYSTEM)
    {
        return $this->getSubject()->getSystemLogger($name);
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    protected function getSystemLoggers()
    {
        return $this->getSubject()->getSystemLoggers();
    }

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    protected function getMultipleFieldDelimiter()
    {
        return $this->getSubject()->getMultipleFieldDelimiter();
    }

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     */
    protected function hasHeader($name)
    {
        return $this->getSubject()->hasHeader($name);
    }

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     */
    protected function getHeader($name)
    {
        return $this->getSubject()->getHeader($name);
    }

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     */
    protected function addHeader($name)
    {
        return $this->getSubject()->addHeader($name);
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    protected function getLastEntityId()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    protected function getSourceDateFormat()
    {
        return $this->getSubject()->getSourceDateFormat();
    }

    /**
     * Cast's the passed value based on the backend type information.
     *
     * @param string $backendType The backend type to cast to
     * @param mixed  $value       The value to be casted
     *
     * @return mixed The casted value
     */
    protected function castValueByBackendType($backendType, $value)
    {
        return $this->getSubject()->castValueByBackendType($backendType, $value);
    }

    /**
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    protected function setStoreViewCode($storeViewCode)
    {
        $this->getSubject()->setStoreViewCode($storeViewCode);
    }

    /**
     * Return's the store view code the create the product/attributes for.
     *
     * @param string|null $default The default value to return, if the store view code has not been set
     *
     * @return string The store view code
     */
    protected function getStoreViewCode($default = null)
    {
        return $this->getSubject()->getStoreViewCode($default);
    }

    /**
     * Prepare's the store view code in the subject.
     *
     * @return void
     */
    protected function prepareStoreViewCode()
    {

        // re-set the store view code
        $this->setStoreViewCode(null);

        // initialize the store view code
        if ($storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE)) {
            $this->setStoreViewCode($storeViewCode);
        }
    }

    /**
     * Tries to format the passed value to a valid date with format 'Y-m-d H:i:s'.
     * If the passed value is NOT a valid date, NULL will be returned.
     *
     * @param string|null $value The value to format
     *
     * @return string The formatted date
     */
    protected function formatDate($value)
    {

        // create a DateTime instance from the passed value
        if ($dateTime = \DateTime::createFromFormat($this->getSourceDateFormat(), $value)) {
            return $dateTime->format('Y-m-d H:i:s');
        }

        // return NULL, if the passed value is NOT a valid date
        return null;
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string      $value     The value to extract
     * @param string|null $delimiter The delimiter used to extrace the elements
     *
     * @return array The exploded values
     */
    protected function explode($value, $delimiter = null)
    {

        // load the default multiple field delimiter
        if ($delimiter === null) {
            $delimiter = $this->getMultipleFieldDelimiter();
        }

        // explode and return the array with the values, by using the delimiter
        return explode($delimiter, $value);
    }

    /**
     * Query whether or not a value for the column with the passed name exists.
     *
     * @param string $name The column name to query for a valid value
     *
     * @return boolean TRUE if the value is set, else FALSE
     */
    protected function hasValue($name)
    {

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the key for the row
            $headerValue = $this->getHeader($name);

            // query whether the rows column has a vaild value
            return (isset($this->row[$headerValue]) && $this->row[$headerValue] != '');
        }

        // return FALSE if not
        return false;
    }

    /**
     * Set the value in the passed column name.
     *
     * @param string $name  The column name to set the value for
     * @param mixed  $value The value to set
     *
     * @return void
     */
    protected function setValue($name, $value)
    {
        $this->row[$this->getHeader($name)] = $value;
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

        // initialize the value
        $value = null;

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the header value
            $headerValue = $this->getHeader($name);
            // query wheter or not, the value with the requested key is available
            if ((isset($this->row[$headerValue]) && $this->row[$headerValue] != '')) {
                $value = $this->row[$headerValue];
            }
        }

        // query whether or not, a callback has been passed
        if ($value != null && is_callable($callback)) {
            $value = call_user_func($callback, $value);
        }

        // query whether or not
        if ($value == null && $default !== null) {
            $value = $default;
        }

        // return the value
        return $value;
    }

    /**
     * Return's the Magento configuration value.
     *
     * @param string  $path    The Magento path of the requested configuration value
     * @param mixed   $default The default value that has to be returned, if the requested configuration value is not set
     * @param string  $scope   The scope the configuration value has been set
     * @param integer $scopeId The scope ID the configuration value has been set
     *
     * @return mixed The configuration value
     * @throws \Exception Is thrown, if nor a value can be found or a default value has been passed
     */
    protected function getCoreConfigData($path, $default = null, $scope = ScopeKeys::SCOPE_DEFAULT, $scopeId = 0)
    {
        return $this->getSubject()->getCoreConfigData($path, $default, $scope, $scopeId);
    }

    /**
     * Initialize's and return's a new entity with the status 'create'.
     *
     * @param array $attr The attributes to merge into the new entity
     *
     * @return array The initialized entity
     */
    protected function initializeEntity(array $attr = array())
    {
        return array_merge(array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE), $attr);
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * status to 'update'.
     *
     * @param array $entity The entity to merge the attributes into
     * @param array $attr   The attributes to be merged
     *
     * @return array The merged entity
     */
    protected function mergeEntity(array $entity, array $attr)
    {
        return array_merge($entity, $attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE));
    }
}
