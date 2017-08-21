<?php

/**
 * TechDivision\Import\Adapter\CsvExportAdapter
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

namespace TechDivision\Import\Adapter;

use Goodby\CSV\Export\Protocol\ExporterInterface;

/**
 * CSV export adapter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvExportAdapter implements ExportAdapterInterface
{

    /**
     * The exporter instance.
     *
     * @var \Goodby\CSV\Export\Protocol\ExporterInterface
     */
    protected $exporter;

    /**
     * The array with the names of the exported files.
     *
     * @var array
     */
    protected $exportedFilenames = array();

    /**
     * Initialize the adapter with the configuration.
     *
     * @param \Goodby\CSV\Export\Protocol\ExporterInterface $exporter The exporter instance
     */
    public function __construct(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * Imports the content of the CSV file with the passed filename.
     *
     * @param array   $artefacts The artefacts to be exported
     * @param string  $targetDir The target dir to export the artefacts to
     * @param integer $timestamp The timestamp part of the original import file
     * @param string  $counter   The counter part of the origin import file
     *
     * @return void
     */
    public function export(array $artefacts, $targetDir, $timestamp, $counter)
    {

        // reset the array with the exported filename
        $this->exportedFilenames = array();

        // iterate over the artefacts and export them
        foreach ($artefacts as $artefactType => $artefacts) {
            // initialize the bunch and the exporter
            $bunch = array();

            // iterate over the artefact types artefacts
            foreach ($artefacts as $entityArtefacts) {
                // prepend the bunch header first
                if (sizeof($bunch) === 0) {
                    $bunch[] = array_keys(reset($entityArtefacts));
                }

                // export the artefacts
                foreach ($entityArtefacts as $entityArtefact) {
                    array_push($bunch, $entityArtefact);
                }
            }

            // prepare the name of the export file
            $filename = sprintf(
                '%s/%s_%s_%s.csv',
                $targetDir,
                $artefactType,
                $timestamp,
                $counter
            );

            // export the artefact (bunch)
            $this->exporter->export($filename, $bunch);

            // add the filename to the array with the exported filenames
            $this->exportedFilenames[] = $filename;
        }
    }

    /**
     * Return's the array with the names of the exported files.
     *
     * @return array The array with the exported filenames
     */
    public function getExportedFilenames()
    {
        return $this->exportedFilenames;
    }
}
