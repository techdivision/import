<?php

/**
 * TechDivision\Import\Adapter\CsvImportAdapter
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

use Goodby\CSV\Import\Protocol\LexerInterface;
use Goodby\CSV\Import\Protocol\InterpreterInterface;

/**
 * CSV import adapter implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CsvImportAdapter implements ImportAdapterInterface
{

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
