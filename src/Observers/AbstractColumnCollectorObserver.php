<?php

/**
 * TechDivision\Import\Observers\GenericColumnCollectorObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Interfaces\HookAwareInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * Observer that loads configurable data into the registry.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractColumnCollectorObserver extends AbstractObserver implements HookAwareInterface, ObserverFactoryInterface
{

    /**
     * The loader instance for the custom validations.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    private $loader;

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    private $registryProcessor;

    /**
     * The flag to query whether or not the value has to be validated on the main row only.
     *
     * @var boolean
     */
    private $mainRowOnly = false;

    /**
     * The array with the column names to assemble the data for.
     *
     * @var array
     */
    private $columnNames = array();

    /**
     * The array with the collected column values.
     *
     * @var array
     */
    private $values = array();

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface             $loader            The loader for the validations
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     * @param boolean                                                  $mainRowOnly       The flag to decide whether or not only values of the main row has to be
     */
    public function __construct(
        LoaderInterface $loader,
        RegistryProcessorInterface $registryProcessor,
        bool $mainRowOnly = false
    ) {
        $this->loader = $loader;
        $this->mainRowOnly = $mainRowOnly;
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject) : ObserverInterface
    {

        // load the names of the columns we want to collect the values for
        $this->columnNames = $this->getLoader()->load($subject->getConfiguration());

        // return the initialized observer instance
        return $this;
    }

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject) : array
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
     * Return's the loader instance for the custom validations.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader() : LoaderInterface
    {
        return $this->loader;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The processor instance
     */
    protected function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
    }

    /**
     * Query whether or not we've to parse the main row only.
     *
     * @return bool TRUE if only the main row has to be parsed, else FALSE
     */
    protected function useMainRowOnly() : bool
    {
        return $this->mainRowOnly;
    }

    /**
     * Return's the primary key value that will be used as second incdex.
     *
     * @return string The primary key to be used
     */
    abstract protected function getPrimaryKey() : string;

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process() : void
    {

        // query whether or not we've
        // to parse the main row only
        if ($this->useMainRowOnly()) {
            // load the store view code to figure out if we're on a main row or not
            $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE, StoreViewCodes::DEF);
            // query whether or not we're in the
            // main row, if not stop processing
            if ($storeViewCode !== StoreViewCodes::DEF) {
                return;
            }
        }

        // load the names of the columns we want to collect the values for
        $columnNames = $this->getLoader()->load($this->getSubject()->getConfiguration());

        // collect the values using the column name and the primary
        // key name as indexes to allow fast access to the values
        foreach ($columnNames as $columnName) {
            $this->values[$columnName][$this->getPrimaryKey()] = $this->getValue($columnName);
        }
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {
        // do nothing here
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // load the registry processor
        $this->getRegistryProcessor()->mergeAttributesRecursive(RegistryKeys::COLLECTED_COLUMNS, $this->values);

        // log a debug message that the observer
        // successfully updated the status data
        $this->getSystemLogger()->notice(
            sprintf(
                'Observer "%s" successfully updated status data for "%s" with "%d" rows',
                get_class($this),
                RegistryKeys::COLLECTED_COLUMNS,
                sizeof($this->values)
            )
        );
    }
}
