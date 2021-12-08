<?php

/**
 * TechDivision\Import\Adapter\Csv\LexerConfigFactory
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

use Goodby\CSV\Import\Standard\LexerConfig;
use TechDivision\Import\Configuration\CsvConfigurationInterface;

/**
 * Factory implementation for a CSV lexer configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class LexerConfigFactory implements LexerConfigFactoryInterface
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
     * Factory method to create a new lexer configuration instance.
     *
     * @return \Goodby\CSV\Import\Standard\LexerConfig The lexer configuration
     */
    public function createLexerConfig()
    {

        // initialize the lexer configuration
        $config = new LexerConfig();

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

        // return the lexer configuratio
        return $config;
    }
}
