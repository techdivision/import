<?php

/**
 * TechDivision\Import\Adapter\Goodby\Lexer
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

use Goodby\CSV\Import\Protocol\LexerInterface;
use Goodby\CSV\Import\Protocol\InterpreterInterface;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\StreamFilter\ConvertMbstringEncoding;

/**
 * Custom exporter implementation which resets row consistency on every import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class Lexer implements LexerInterface
{

    /**
     * The exporter configuration.
     *
     * @var \Goodby\CSV\Import\Standard\LexerConfig
     */
    private $config;

    /**
     * Initialize the instance with the passed configuration.
     *
     * @param \Goodby\CSV\Import\Standard\LexerConfig $config The lexer configuration
     */
    public function __construct(LexerConfig $config = null)
    {

        // query whether or not a configuration has been passed
        if ($config instanceof LexerConfig) {
            $this->config = $config;
        } else {
            $this->config = new LexerConfig();
        }

        // register the encoding filter
        ConvertMbstringEncoding::register();
    }

    /**
     * Parse the passed CSV file.
     *
     * @param string                                           $filename    The filename to parse
     * @param \Goodby\CSV\Import\Protocol\InterpreterInterface $interpreter The interpreter instance
     *
     * @return void
     */
    public function parse($filename, InterpreterInterface $interpreter)
    {

        // for mac's office excel csv
        ini_set('auto_detect_line_endings', true);

        // initialize the configuration
        $delimiter      = $this->config->getDelimiter();
        $enclosure      = $this->config->getEnclosure();
        $escape         = $this->config->getEscape();
        $fromCharset    = $this->config->getFromCharset();
        $toCharset      = $this->config->getToCharset();
        $flags          = $this->config->getFlags();
        $ignoreHeader   = $this->config->getIgnoreHeaderLine();

        // query whether or not the charset has to be converted
        if ($fromCharset === null) {
            $url = $filename;
        } else {
            $url = ConvertMbstringEncoding::getFilterURL($filename, $fromCharset, $toCharset);
        }

        // initialize the CSV file object
        $csv = new \SplFileObject($url);
        $csv->setCsvControl($delimiter, $enclosure, $escape);
        $csv->setFlags($flags);

        // backup current locale
        $originalLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');

        // process each line of the CSV file
        foreach ($csv as $lineNumber => $line) {
            if ($ignoreHeader && $lineNumber == 0 || (count($line) === 1 && trim($line[0]) === '')) {
                continue;
            }
            $interpreter->interpret($line);
        }

        // reset locale
        parse_str(str_replace(';', '&', $originalLocale), $locale_array);
        setlocale(LC_ALL, $locale_array);
    }
}
