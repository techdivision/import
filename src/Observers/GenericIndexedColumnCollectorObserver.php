<?php

/**
 * TechDivision\Import\Observers\GenericIndexedColumnCollectorObserver
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Observer that loads the data of a set of configurable columns
 * into the registry for further processing, e. g. validation.
 *
 * As index the column name and the value of the configurable
 * primary key column will be used. This makes it possible to
 * access the collected data later on with this primary key,
 * for example you want to know the frontend input type of the
 * attribute with the attribute code that has been configured
 * as primary key column.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericIndexedColumnCollectorObserver extends AbstractColumnCollectorObserver
{

    /**
     * The column name that contains the primary key of the entity we want to process.
     *
     * @var string
     */
    private $primaryKeyColumn;

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface             $loader            The loader for the validations
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     * @param boolean                                                  $mainRowOnly       The flag to decide whether or not only values of the main row has to be collected
     * @param string                                                   $primaryKeyColumn  The column that provides the primary key
     */
    public function __construct(
        LoaderInterface $loader,
        RegistryProcessorInterface $registryProcessor,
        bool $mainRowOnly = false,
        string $primaryKeyColumn = ColumnKeys::ATTRIBUTE_CODE
    ) {

        // initialize the primary key column, by default
        // we use the column `attribute_code`
        $this->primaryKeyColumn = $primaryKeyColumn;

        // pass the loader,the registry processor and
        // the main row flag to the parent instance
        parent::__construct($loader, $registryProcessor, $mainRowOnly);
    }

    /**
     * Return's the configured primary key column.
     *
     * @return string
     */
    protected function getPrimaryKeyColumn() : string
    {
        return $this->primaryKeyColumn;
    }

    /**
     * Return's the primary key value that will be used as second incdex.
     *
     * @return string The primary key to be used
     */
    protected function getPrimaryKey() : string
    {
        return $this->getValue($this->getPrimaryKeyColumn());
    }
}
