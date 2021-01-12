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
use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface;

/**
 * CSV export adapter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvExportAdapter implements ExportAdapterInterface, SerializerAwareAdapterInterface
{

    /**
     * The trait that provides serializer functionality.
     *
     * @var \TechDivision\Import\Adapter\SerializerTrait
     */
    use SerializerTrait;

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
     * Overwrites the default CSV configuration values with the one from the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface $exportAdapterConfiguration The configuration to use the values from
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface                     $serializerFactory          The serializer factory instance
     *
     * @return void
     */
    public function init(
        ExportAdapterConfigurationInterface $exportAdapterConfiguration,
        SerializerFactoryInterface $serializerFactory
    ) {

        // load the exporter configuration and overwrite the values
        /** @var \Goodby\CSV\Export\Standard\ExporterConfig $config */
        $config = $this->exporter->getConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $exportAdapterConfiguration->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $exportAdapterConfiguration->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $exportAdapterConfiguration->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $exportAdapterConfiguration->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $exportAdapterConfiguration->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // query whether or not a custom file mode has been configured
        if ($fileMode = $exportAdapterConfiguration->getFileMode()) {
            $config->setFileMode($fileMode);
        }

        // load the serializer instance from the DI container and set it on the subject instance
        $this->setSerializer($serializerFactory->createSerializer($exportAdapterConfiguration));
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
