<?php

/**
 * TechDivision\Import\Handlers\GenericFileHandlerInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

/**
 * A generic interface for file handler implementations implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface GenericFileHandlerInterface extends HandlerInterface
{

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed from
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\LineNotFoundException Is thrown, if the requested line can not be found in the file
     * @throws \Exception Is thrown, if the file can not be written, after the line has been removed
     * @see \TechDivision\Import\Handlers\GenericFileHandlerInterface::removeLineFromFile()
     */
    public function removeLineFromFilename(string $line, string $filename) : void;

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string   $line The line to be removed
     * @param resource $fh   The file handle of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    public function removeLineFromFile(string $line, $fh) : void;
}
