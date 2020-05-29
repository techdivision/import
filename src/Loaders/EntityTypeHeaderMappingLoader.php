<?php

/**
 * TechDivision\Import\Loaders\EntityTypeHeaderMappingLoader
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * A loader that loads the header mappings from the configuration using a specific entity type
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class EntityTypeHeaderMappingLoader implements LoaderInterface
{

    /**
     * The header mappings for the entity type with the code passed to the constructor.
     *
     * @var array
     */
    private $headerMappings;

    /**
     * Initialize the loader with the parent loader and the entity type code.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $parentLoader   The parent loader for the header mappings
     * @param string                                       $entityTypeCode The entity type code to load the header mappings for
     */
    public function __construct(LoaderInterface $parentLoader, string $entityTypeCode)
    {
        $this->headerMappings = $parentLoader->load($entityTypeCode);
    }

    /**
     * Loads and returns data.
     *
     * @return array The array with the raw data
     */
    public function load() : array
    {
        return $this->headerMappings;
    }
}
