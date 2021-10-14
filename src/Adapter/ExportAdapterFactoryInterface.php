<?php

/**
 * TechDivision\Import\Adapter\ExportAdapterFactoryInterface
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

use TechDivision\Import\Configuration\ExportAdapterAwareConfigurationInterface;

/**
 * Interface for all CSV export adapter factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ExportAdapterFactoryInterface
{

    /**
     * Creates and returns the export adapter for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\ExportAdapterAwareConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The export adapter instance
     */
    public function createExportAdapter(ExportAdapterAwareConfigurationInterface $configuration);
}
