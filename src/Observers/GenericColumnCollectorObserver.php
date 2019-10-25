<?php

/**
 * TechDivision\Import\Observers\GenericColumnCollectorObserver
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use Ramsey\Uuid\Uuid;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Observer that loads configurable data into the registry.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericColumnCollectorObserver extends AbstractObserver
{

    /**
     * The loader instance for the custom validations.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface             $loader            The loader for the validations
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(LoaderInterface $loader, RegistryProcessorInterface $registryProcessor)
    {
        $this->loader = $loader;
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
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
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     * @throws \Exception Is thrown, if the product with the SKU can not be loaded
     */
    protected function process()
    {

        // initialize the array for the column values
        $values = array();

        // load the names of the columns we want to collect the values for
        $columnNames = $this->getLoader()->load($this->getSubject()->getConfiguration());

        // collect the values
        foreach ($columnNames as $columnName) {
            $values[$columnName] = array(Uuid::uuid4()->toString() => $this->getValue($columnName));
        }

        // merge the collected values into the registry
        $this->mergeStatus(array(RegistryKeys::COLLECTED_COLUMNS => $values));
    }
}
