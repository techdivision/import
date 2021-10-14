<?php

/**
 * TechDivision\Import\Loaders\CollectionFilteredByExecutionContextEntityTypeLoader
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

/**
 * Loader for data that has been filtered by the passed entity type code.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * Construct that initializes the iterator with the parent loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader The parent loader instance
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Loads and returns data the array with the data, filtered by the passed entity type code.
     *
     * @param string|null $entityTypeCode The entity type code to filter the data with
     *
     * @return \ArrayAccess The array with the data
     */
    public function load($entityTypeCode = null)
    {

        // load the data we want to filter by the passed entity type code
        $data = $this->loader->load();

        // query whether or not data with the passed entity type code is available
        if (isset($data[$entityTypeCode])) {
            return $data[$entityTypeCode];
        }

        // return an empty array otherwise
        return array();
    }
}
