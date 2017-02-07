<?php

/**
 * TechDivision\Import\Subjects\ExportableTrait
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

namespace TechDivision\Import\Subjects;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;

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
     * The array containing the data for product type configuration (configurables, bundles, etc).
     *
     * @var array
     */
    protected $artefacs = array();

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts()
    {
        return $this->artefacs;
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param string $type      The artefact type, e. g. configurable
     * @param array  $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts($type, array $artefacts)
    {

        // query whether or not, any artefacts are available
        if (sizeof($artefacts) === 0) {
            return;
        }

        // append the artefacts to the stack
        $this->artefacs[$type][$this->getLastEntityId()][] = $artefacts;
    }

    /**
     * Export's the artefacts to CSV files.
     *
     * @param integer $timestamp The timestamp part of the original import file
     * @param string  $counter   The counter part of the origin import file
     *
     * @return void
     */
    public function export($timestamp, $counter)
    {

        // load the target directory and the actual timestamp
        $targetDir = $this->getTargetDir();

        // iterate over the artefacts and export them
        foreach ($this->getArtefacts() as $artefactType => $artefacts) {
            // initialize the bunch and the exporter
            $bunch = array();
            $exporter = new Exporter($this->getExportConfig());

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
            $exporter->export(sprintf('%s/%s_%s_%s.csv', $targetDir, $artefactType, $timestamp, $counter), $bunch);
        }
    }

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    protected function getTargetDir()
    {
        return $this->getNewSourceDir();
    }

    /**
     * Initialize and return the exporter configuration.
     *
     * @return \Goodby\CSV\Export\Standard\ExporterConfig The exporter configuration
     */
    protected function getExportConfig()
    {

        // initialize the lexer configuration
        $config = new ExporterConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $this->getConfiguration()->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $this->getConfiguration()->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $this->getConfiguration()->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $this->getConfiguration()->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $this->getConfiguration()->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // query whether or not a custom file mode has been configured
        if ($fileMode = $this->getConfiguration()->getFileMode()) {
            $config->setFileMode($fileMode);
        }

        // return the lexer configuratio
        return $config;
    }
}
