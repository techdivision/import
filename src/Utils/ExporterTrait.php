<?php

/**
 * TechDivision\Import\Utils\ExporterTrait
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

namespace TechDivision\Import\Utils;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;

/**
 * The trait implementation for the exporter initialization functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait ExporterTrait
{

    /**
     * Initialize and return a new exporter instance.
     *
     * @return \Goodby\CSV\Export\Standard\Exporter The exporter instance
     */
    protected function getExporterInstance()
    {
        return new Exporter($this->getExportConfig());
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
