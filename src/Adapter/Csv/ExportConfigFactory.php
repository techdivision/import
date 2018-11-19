<?php

/**
 * TechDivision\Import\Adapter\Csv\ExportConfigFactory
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

namespace TechDivision\Import\Adapter\Csv;

use Goodby\CSV\Export\Standard\ExporterConfig;
use TechDivision\Import\Configuration\CsvConfigurationInterface;

/**
 * Factory implementation for a CSV export configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ExportConfigFactory implements ExportConfigFactoryInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\CsvConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the adapter with the configuration.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The configuration instance
     */
    public function __construct(CsvConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Factory method to create a new export configuration instance.
     *
     * @return \Goodby\CSV\Export\Standard\ExporterConfig The export configuration
     */
    public function createExportConfig()
    {

        // initialize the lexer configuration
        $config = new ExporterConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $this->configuration->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $this->configuration->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $this->configuration->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $this->configuration->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $this->configuration->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // query whether or not a custom file mode has been configured
        if ($fileMode = $this->configuration->getFileMode()) {
            $config->setFileMode($fileMode);
        }

        // return the lexer configuration
        return $config;
    }
}
