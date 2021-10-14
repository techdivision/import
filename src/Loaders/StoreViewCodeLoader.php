<?php

/**
 * TechDivision\Import\Loaders\StoreViewCodeLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for store view codes.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 17.0.0 use \TechDivision\Import\Loaders\GenericMemberNameLoader instead
 */
class StoreViewCodeLoader implements LoaderInterface
{

    /**
     * The store view codes.
     *
     * @var array
     */
    protected $stores = array();

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // load the stores
        $stores = $importProcessor->getStores();

        // initialize the array with the store websites
        foreach ($stores as $store) {
            $this->stores[] = $store[MemberNames::CODE];
        }
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->stores;
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The processor instance
     */
    protected function getImportProcessor()
    {
        return $this->importProcessor;
    }
}
