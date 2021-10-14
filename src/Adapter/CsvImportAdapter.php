<?php

/**
 * TechDivision\Import\Adapter\CsvImportAdapter
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter;

use Goodby\CSV\Import\Protocol\LexerInterface;
use Goodby\CSV\Import\Protocol\InterpreterInterface;
use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface;

/**
 * CSV import adapter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvImportAdapter implements ImportAdapterInterface
{

    /**
     * The trait that provides serializer functionality.
     *
     * @var \TechDivision\Import\Adapter\SerializerTrait
     */
    use SerializerTrait;

    /**
     * The lexer instance.
     *
     * @var \Goodby\CSV\Import\Protocol\LexerInterface
     */
    protected $lexer;

    /**
     * The interpreter instance.
     *
     * @var \Goodby\CSV\Import\Protocol\InterpreterInterface
     */
    protected $interpreter;

    /**
     * The adapter's serializer instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * Initialize the adapter with the configuration.
     *
     * @param \Goodby\CSV\Import\Protocol\LexerInterface       $lexer       The lexer instance
     * @param \Goodby\CSV\Import\Protocol\InterpreterInterface $interpreter The interpreter instance
     */
    public function __construct(LexerInterface $lexer, InterpreterInterface $interpreter)
    {
        $this->lexer = $lexer;
        $this->interpreter = $interpreter;
    }

    /**
     * Overwrites the default CSV configuration values with the one from the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface $importAdapterConfiguration The configuration to use the values from
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface                     $serializerFactory          The serializer factory instance
     *
     * @return void
     */
    public function init(
        ImportAdapterConfigurationInterface $importAdapterConfiguration,
        SerializerFactoryInterface $serializerFactory
    ) {

        // load the lexer configuration and overwrite the values
        /** @var \Goodby\CSV\Import\Standard\LexerConfig $config */
        $config = $this->lexer->getConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $importAdapterConfiguration->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $importAdapterConfiguration->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $importAdapterConfiguration->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $importAdapterConfiguration->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $importAdapterConfiguration->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // load the serializer instance from the DI container and set it on the subject instance
        $this->setSerializer($serializerFactory->createSerializer($importAdapterConfiguration));
    }

    /**
     * Imports the content of the CSV file with the passed filename.
     *
     * @param callable $callback The callback that processes the row
     * @param string   $filename The filename to process
     *
     * @return void
     */
    public function import(callable $callback, $filename)
    {

        // reset and initialize the interpreter
        $this->interpreter->reset();
        $this->interpreter->addObserver($callback);

        // parse the CSV file to be imported
        $this->lexer->parse($filename, $this->interpreter);
    }
}
