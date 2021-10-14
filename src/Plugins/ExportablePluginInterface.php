<?php

/**
 * TechDivision\Import\Plugins\ExportablePluginInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Adapter\ExportAdapterInterface;

/**
 * The interface for all plugin implementations that supports an export adapter.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ExportablePluginInterface
{

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts();

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    public function getTargetDir();

    /**
     * Reset the array with the artefacts to free the memory.
     *
     * @return void
     */
    public function resetArtefacts();

    /**
     * Export's the artefacts to CSV files and resets the array with the artefacts to free the memory.
     *
     * @param integer $timestamp The timestamp part of the original import file
     * @param string  $counter   The counter part of the origin import file
     *
     * @return void
     */
    public function export($timestamp, $counter);

    /**
     * Set's the exporter adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ExportAdapterInterface $exportAdapter The exporter adapter instance
     *
     * @return void
     */
    public function setExportAdapter(ExportAdapterInterface $exportAdapter);

    /**
     * Return's the exporter adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The exporter adapter instance
     */
    public function getExportAdapter();
}
