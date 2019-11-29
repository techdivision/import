<?php

/**
 * TechDivision\Import\Loaders\CollectionFilteredByExecutionContextEntityTypeLoader
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

namespace TechDivision\Import\Loaders;

/**
 * Loader for data that has been filtered by the passed entity type code.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FilteredByEntityTypeCodeLoader implements LoaderInterface
{

    /**
     * The attribute sets.
     *
     * @var array
     */
    protected $loader;

    /**
     * Construct that initializes the iterator with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Loads and returns data the array with the data, filtered by the passed entity type code.
     *
     * @param string $entityTypeCode The entity type code to filter the data with
     *
     * @return \ArrayAccess The array with the data
     */
    public function load($entitTypeCode = null)
    {

        // load the data we want to filter by the passed entity type code
        $data = $this->loader->load();

        // query whether or not data with the passed entity type code is available
        if (isset($data[$entitTypeCode])) {
            return $data[$entitTypeCode];
        }

        // return an empty array otherwise
        return array();
    }
}
