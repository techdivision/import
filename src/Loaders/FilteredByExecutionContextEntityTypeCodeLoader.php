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

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for data that has been filtered by the entity type code found in the execution context of the passed subject configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FilteredByExecutionContextEntityTypeCodeLoader implements LoaderInterface
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
     * Loads and returns data the custom validation data.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the validations from
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(SubjectConfigurationInterface $configuration = null)
    {
        return $this->loader->load($configuration->getExecutionContext()->getEntityTypeCode());
    }
}
