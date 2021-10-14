<?php

/**
 * TechDivision\Import\Adapter\Csv\LexerConfigFactoryInterface
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

/**
 * Interface for all CSV lexer configuration factory implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface LexerConfigFactoryInterface
{

    /**
     * Factory method to create a new lexer configuration instance.
     *
     * @return \Goodby\CSV\Import\Standard\LexerConfig The lexer configuration
     */
    public function createLexerConfig();
}
