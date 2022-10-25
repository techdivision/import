<?php

/**
 * TechDivision\Import\Adapter\Goodby\Lexer
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
 * @license   https://opensource.org/licenses/MIT
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
     * Returns the lexer configuration.
     *
     * @return \Goodby\CSV\Import\Standard\LexerConfig The configuration instance
     */
    public function getConfig()
    {
        return $this->config;
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
        @ini_set('auto_detect_line_endings', true);

        // initialize the configuration
        $delimiter      = $this->config->getDelimiter();
        $enclosure      = $this->config->getEnclosure();
        $escape         = $this->config->getEscape();
        $escape         = empty($escape) ? "\0" : $escape;
        $fromCharset    = $this->config->getFromCharset();
        $toCharset      = $this->config->getToCharset();
        $flags           = $this->config->getFlags();
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

        // http://en.wikipedia.org/wiki/Byte_order_mark#UTF-8
        $bom = pack('CCC', 0xEF, 0xBB, 0xBF);

        // process each line of the CSV file
        foreach ($csv as $lineNumber => $line) {
            if ($lineNumber == 0 && isset($line[0])) {
                // remove windwos BOM if exists
                if (substr($line[0], 0, 3) === $bom) {
                    $line[0] = substr($line[0], 3);
                }
                // Remove quotes in first row first cell
                if (strpos($line[0], '"') !== false) {
                    $line[0] = str_replace('"', '', $line[0]);
                }
            }
            if ($ignoreHeader && $lineNumber == 0
                || (count($line) === 1
                     && ($line[0] === null || trim($line[0]) === '')
                   )
                ) {
                continue;
            }
            $interpreter->interpret($line);
        }

        // reset locale
        $localeArray = array();
        parse_str(str_replace(';', '&', $originalLocale), $localeArray);
        setlocale(LC_ALL, $localeArray);
    }
}
