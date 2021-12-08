<?php

/**
 * TechDivision\Import\Adapter\ImportAdapterFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter;

use TechDivision\Import\Configuration\ImportAdapterAwareConfigurationInterface;

/**
 * Interface for all CSV import adapter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ImportAdapterFactoryInterface
{

    /**
     * Creates and returns the import adapter for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\ImportAdapterAwareConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The import adapter instance
     */
    public function createImportAdapter(ImportAdapterAwareConfigurationInterface $configuration);
}
