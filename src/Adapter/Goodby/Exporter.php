<?php

/**
 * TechDivision\Import\Adapter\Goodby\Exporter
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

namespace TechDivision\Import\Adapter\Goodby;

use Goodby\CSV\Export\Protocol\ExporterInterface;
use Goodby\CSV\Export\Protocol\Exception\IOException;
use Goodby\CSV\Export\Standard\CsvFileObject;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Exception\StrictViolationException;

/**
 * Custom exporter implementation which resets row consistency on every import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class Exporter implements ExporterInterface
{

    /**
     * The exporter configuration.
     *
     * @var \Goodby\CSV\Export\Standard\ExporterConfig
     */
    private $config;

    /**
     * The number of rows found in the header.
     *
     * @var int
     */
    private $rowConsistency = null;

    /**
     * Query whether or not strict mode is activated or not.
     *
     * @var boolean
     */
    private $strict = true;

    /**
     * Initialize the instance with the passed configuration.
     *
     * @param \Goodby\CSV\Export\Standard\ExporterConfig $config The exporter configuration
     */
    public function __construct(ExporterConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Disable strict mode.
     *
     * @return void
     */
    public function unstrict()
    {
        $this->strict = false;
    }

    /**
     * Query whether or not strict mode has been activated.
     *
     * @return boolean TRUE if the strict mode has NOT been activated, else FALSE
     */
    private function isNotStrict()
    {
        return $this->strict === false;
    }

    /**
     * Returns the exporter configuration.
     *
     * @return \Goodby\CSV\Export\Standard\ExporterConfig The configuration instance
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Export data as CSV file.
     *
     * @param string $filename The filename to export to
     * @param array  $rows     The rows to export
     *
     * @return void
     * @throws \Goodby\CSV\Export\Protocol\Exception\IOException
     */
    public function export($filename, $rows)
    {

        // reset the exporter
        $this->reset();

        // initialize the configuration
        $delimiter     = $this->config->getDelimiter();
        $enclosure     = $this->config->getEnclosure();
        $enclosure     = empty($enclosure) ? "\0" : $enclosure;
        $newline       = $this->config->getNewline();
        $fromCharset   = $this->config->getFromCharset();
        $toCharset     = $this->config->getToCharset();
        $fileMode      = $this->config->getFileMode();
        $columnHeaders = $this->config->getColumnHeaders();

        try {
            $csv = new CsvFileObject($filename, $fileMode);
        } catch (\Exception $e) {
            throw new IOException($e->getMessage(), null, $e);
        }

        // set the new line char
        $csv->setNewline($newline);

        // query whether we've to convert the charset
        if ($toCharset) {
            $csv->setCsvFilter(function ($line) use ($toCharset, $fromCharset) {
                return mb_convert_encoding($line, $toCharset, $fromCharset);
            });
        }

        // export the header
        if (count($columnHeaders) > 0) {
            $this->checkRowConsistency($columnHeaders);
            $csv->fputcsv($columnHeaders, $delimiter, $enclosure);
        }

        // export the rows
        foreach ($rows as $row) {
            $this->checkRowConsistency($row);
            $csv->fputcsv($row, $delimiter, $enclosure);
        }

        // flush the CSV file
        $csv->fflush();
    }

    /**
     * Reset the interpreter.
     *
     * @return void
     */
    public function reset()
    {
        $this->rowConsistency = null;
    }

    /**
     * Check if the column count is consistent with comparing other rows.
     *
     * @param array $row The row to check consistency for
     *
     * @return void
     * @throws \Goodby\CSV\Export\Standard\Exception\StrictViolationException Is thrown, if row consistency check fails
     */
    private function checkRowConsistency(array $row)
    {

        // query whether or not strict mode is enabled
        if ($this->isNotStrict()) {
            return;
        }

        // count the number of columns
        $current = count($row);

        // if the row consistency has not been set, set it
        if ($this->rowConsistency === null) {
            $this->rowConsistency = $current;
        }

        // check row consistency
        if ($current !== $this->rowConsistency) {
            throw new StrictViolationException(sprintf('Column size should be %u, but %u columns given', $this->rowConsistency, $current));
        }

        // set the new row consistency
        $this->rowConsistency = $current;
    }
}
