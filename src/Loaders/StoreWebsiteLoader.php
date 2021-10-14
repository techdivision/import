<?php

/**
 * TechDivision\Import\Loaders\StoreWebsiteLoader
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
 * Loader for store websites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StoreWebsiteLoader implements LoaderInterface
{

    /**
     * The store websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // load the store websites
        $storeWebsites = $importProcessor->getStoreWebsites();

        // initialize the array with the store websites
        foreach ($storeWebsites as $storeWebsite) {
            $this->storeWebsites[] = $storeWebsite[MemberNames::CODE];
        }
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->storeWebsites;
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
