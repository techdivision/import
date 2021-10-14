<?php

/**
 * TechDivision\Import\Utils\DebugUtilInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Interface for debug util implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface DebugUtilInterface
{

    /**
     * The method to extract an archive that has already been created by a previous
     * import back into the source directory.
     *
     * @param string $serial The serial of the archive to extract
     *
     * @return void
     * @throws \Exception Is thrown, if the archive can not be extracted back into the source directory
     */
    public function extractArchive(string $serial) : void;

    /**
     * The method to create the debugging artefacts in the apropriate directory.
     *
     * @param string $serial The serial to prepare the dump for
     *
     * @return void
     * @throws \Exception Is thrown, if the configuration can not be dumped
     */
    public function prepareDump(string $serial) : void;

    /**
     * The method to create the debug dump with all artefacts and reports.
     *
     * @param string $serial The serial to create the dump for
     *
     * @return string $filename The name of the dumpfile
     * @throws \InvalidArgumentException Is thrown, if the passed serial has no matching import to create the dump for
     */
    public function createDump(string $serial) : string;
}
