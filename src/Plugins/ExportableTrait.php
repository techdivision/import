<?php

/**
 * TechDivision\Import\Plugins\ExportableTrait
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Adapter\ExportAdapterInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * The trait implementation for the artefact export functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait ExportableTrait
{

    /**
     * The export adapter instance.
     *
     * @var \TechDivision\Import\Adapter\ExportAdapterInterface
     */
    protected $exportAdapter;

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    abstract public function getArtefacts();

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    abstract public function getTargetDir();

    /**
     * Reset the array with the artefacts to free the memory.
     *
     * @return void
     */
    abstract public function resetArtefacts();

    /**
     * Export's the artefacts to CSV files and resets the array with the artefacts to free the memory.
     *
     * @param integer $timestamp The timestamp part of the original import file
     * @param string  $counter   The counter part of the origin import file
     *
     * @return void
     */
    public function export($timestamp, $counter)
    {

        // export the artefacts
        $this->getExportAdapter()->export($this->getArtefacts(), $this->getTargetDir(), $timestamp, $counter);

        // initialize the array with the status
        $status = array();

        // add the exported artefacts to the status
        foreach ($this->getExportAdapter()->getExportedFilenames() as $filename) {
            $status[$filename] = array();
        }

        // update status for the exported files
        $this->getRegistryProcessor()->mergeAttributesRecursive(RegistryKeys::STATUS, array(RegistryKeys::FILES => $status));

        // reset the artefacts
        $this->resetArtefacts();
    }

    /**
     * Set's the exporter adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ExportAdapterInterface $exportAdapter The exporter adapter instance
     *
     * @return void
     */
    public function setExportAdapter(ExportAdapterInterface $exportAdapter)
    {
        $this->exportAdapter = $exportAdapter;
    }

    /**
     * Return's the exporter adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The exporter adapter instance
     */
    public function getExportAdapter()
    {
        return $this->exportAdapter;
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    abstract protected function getRegistryProcessor();
}
