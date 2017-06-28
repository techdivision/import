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

        // iterate over the artefacts and export them
        foreach ($artefacts as $artefactType => $artefacts) {
            // initialize the bunch and the exporter
            $bunch = array();

            // iterate over the artefact types artefacts
            foreach ($artefacts as $entityArtefacts) {
                // set the bunch header and append the artefact data
                if (sizeof($bunch) === 0) {
                    $first = reset($entityArtefacts);
                    $second = reset($first);
                    $bunch[] = array_keys($second);
                }

                // export the artefacts
                foreach ($entityArtefacts as $entityArtefact) {
                    $bunch = array_merge($bunch, $entityArtefact);
                }
            }

            // export the artefact (bunch)
            $this->exporter->export(
                sprintf(
                    '%s/%s_%s_%s.csv',
                    $targetDir,
                    $artefactType,
                    $timestamp,
                    $counter
                ),
                $bunch
            );
        }
    }
}
