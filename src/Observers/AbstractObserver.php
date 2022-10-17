<?php

/**
 * TechDivision\Import\Observers\AbstractObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use TechDivision\Import\RowTrait;
use TechDivision\Import\Utils\ScopeKeys;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * An abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractObserver implements ObserverInterface
{

    /**
     * The trait that provides row handling functionality.
     *
     * @var \TechDivision\Import\RowTrait
     */
    use RowTrait;

    /**
     * The obeserver's subject instance.
     *
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    protected $subject;

    /**
     * The state detector instance.
     *
     * @var \TechDivision\Import\Observers\StateDetectorInterface
     */
    protected $stateDetector;

    /**
     * Initializes the observer with the state detector instance.
     *
     * @param \TechDivision\Import\Observers\StateDetectorInterface $stateDetector The state detector instance
     */
    public function __construct(StateDetectorInterface $stateDetector = null)
    {
        $this->stateDetector = $stateDetector;
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
     * @return object The observer's subject instance
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Return's the observer's state detector instance.
     *
     * @return \TechDivision\Import\Observers\StateDetectorInterface The state detector instance
     */
    protected function getStateDetector()
    {
        return $this->stateDetector;
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
        return array_merge($attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE));
    }

    /**
     * Query whether or not the entity has to be processed.
     *
     * @param array $entity The entity to query for
     *
     * @return boolean TRUE if the entity has to be processed, else FALSE
     */
    protected function hasChanges(array $entity)
    {
        return in_array($entity[EntityStatus::MEMBER_NAME], array(EntityStatus::STATUS_CREATE, EntityStatus::STATUS_UPDATE));
    }

    /**
     * Detect's the entity state on the specific entity conditions and return's it.
     *
     * @param array       $entity        The entity loaded from the database
     * @param array       $attr          The entity data from the import file
     * @param string|null $changeSetName The change set name to use
     *
     * @return string The detected entity state
     */
    protected function detectState(array $entity, array $attr, $changeSetName = null)
    {
        return $this->getStateDetector() instanceof StateDetectorInterface ? $this->getStateDetector()->detect($this, $entity, $attr, $changeSetName) : EntityStatus::STATUS_UPDATE;
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity        The entity to merge the attributes into
     * @param array       $attr          The attributes to be merged
     * @param string|null $changeSetName The change set name to use
     *
     * @return array The merged entity
     */
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null)
    {
        $merged = array_merge($entity, $attr);
        return array_merge($merged, array(EntityStatus::MEMBER_NAME => $this->detectState($entity, $merged, $changeSetName)));
    }

    /**
     * Merge's the passed status into the actual one.
     *
     * @param array $status The status to MergeBuilder
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function mergeStatus(array $status)
    {
        $this->getSubject()->mergeStatus($status);
    }

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function setHeaders(array $headers)
    {
        $this->getSubject()->setHeaders($headers);
    }

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     *
     * @codeCoverageIgnore
     */
    protected function getHeaders()
    {
        return $this->getSubject()->getHeaders();
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     *
     * @codeCoverageIgnore
     */
    protected function getRegistryProcessor()
    {
        return $this->getSubject()->getRegistryProcessor();
    }

    /**
     * Append's the exception suffix containing filename and line number to the
     * passed message. If no message has been passed, only the suffix will be
     * returned
     *
     * @param string|null $message    The message to append the exception suffix to
     * @param string|null $filename   The filename used to create the suffix
     * @param string|null $lineNumber The line number used to create the suffx
     *
     * @return string The message with the appended exception suffix
     *
     * @codeCoverageIgnore
     */
    protected function appendExceptionSuffix($message = null, $filename = null, $lineNumber = null)
    {
        return $this->getSubject()->appendExceptionSuffix($message, $filename, $lineNumber);
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
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
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
     * Stop's observer execution on the actual row.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function skipRow()
    {
        $this->getSubject()->skipRow();
    }

    /**
     * Return's the name of the file to import.
     *
     * @return string The filename
     *
     * @codeCoverageIgnore
     */
    protected function getFilename()
    {
        return $this->getSubject()->getFilename();
    }

    /**
     * Return's the actual line number.
     *
     * @return integer The line number
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
     */
    protected function getSystemLogger($name = LoggerKeys::SYSTEM)
    {
        return $this->getSubject()->getSystemLogger($name);
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     *
     * @codeCoverageIgnore
     */
    protected function getSystemLoggers()
    {
        return $this->getSubject()->getSystemLoggers();
    }

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     *
     * @codeCoverageIgnore
     */
    protected function getMultipleFieldDelimiter()
    {
        return $this->getSubject()->getMultipleFieldDelimiter();
    }

    /**
     * Return's the multiple value delimiter character to use, default value is comma (|).
     *
     * @return string The multiple value delimiter character
     *
     * @codeCoverageIgnore
     */
    protected function getMultipleValueDelimiter()
    {
        return $this->getSubject()->getMultipleValueDelimiter();
    }

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     *
     * @codeCoverageIgnore
     */
    public function hasHeader($name)
    {
        return $this->getSubject()->hasHeader($name);
    }

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * @throws \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     *
     * @codeCoverageIgnore
     */
    public function getHeader($name)
    {
        return $this->getSubject()->getHeader($name);
    }

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     *
     * @codeCoverageIgnore
     */
    protected function addHeader($name)
    {
        return $this->getSubject()->addHeader($name);
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     *
     * @codeCoverageIgnore
     */
    protected function getLastEntityId()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
     */
    public function castValueByBackendType($backendType, $value)
    {
        return $this->getSubject()->castValueByBackendType($backendType, $value);
    }

    /**
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
     */
    protected function getStoreViewCode($default = null)
    {
        return $this->getSubject()->getStoreViewCode($default);
    }

    /**
     * Prepare's the store view code in the subject.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function prepareStoreViewCode()
    {
        $this->getSubject()->prepareStoreViewCode();
    }

    /**
     * Return's the store ID of the store with the passed store view code
     *
     * @param string $storeViewCode The store view code to return the store ID for
     *
     * @return integer The ID of the store with the passed ID
     * @throws \Exception Is thrown, if the store with the actual code is not available
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
     */
    protected function getRowStoreId($default = null)
    {
        return $this->getSubject()->getRowStoreId($default);
    }

    /**
     * Tries to format the passed value to a valid date with format 'Y-m-d H:i:s'.
     * If the passed value is NOT a valid date, NULL will be returned.
     *
     * @param string|null $value The value to format
     *
     * @return string The formatted date
     *
     * @codeCoverageIgnore
     */
    protected function formatDate($value)
    {
        return $this->getSubject()->formatDate($value);
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed delimiter.
     *
     * @param string      $value     The value to extract
     * @param string|null $delimiter The delimiter used to extrace the elements
     *
     * @return array The exploded values
     *
     * @codeCoverageIgnore
     */
    protected function explode($value, $delimiter = null)
    {
        return $this->getSubject()->explode($value, $delimiter);
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
     *
     * @codeCoverageIgnore
     */
    protected function getCoreConfigData($path, $default = null, $scope = ScopeKeys::SCOPE_DEFAULT, $scopeId = 0)
    {
        return $this->getSubject()->getCoreConfigData($path, $default, $scope, $scopeId);
    }
}
