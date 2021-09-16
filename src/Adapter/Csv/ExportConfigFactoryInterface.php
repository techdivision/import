<?php

/**
 * TechDivision\Import\Adapter\Csv\ExportConfigFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter\Csv;

/**
 * Interface for all CSV export configuration factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ExportConfigFactoryInterface
{

    /**
     * Factory method to create a new export configuration instance.
     *
     * @return \Goodby\CSV\Export\Standard\ExporterConfig The export configuration
     */
    public function createExportConfig();
}
