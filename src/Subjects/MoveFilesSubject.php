<?php

/**
 * TechDivision\Import\Cli\Subjects\MoveFilesSubject
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

namespace TechDivision\Import\Subjects;

/**
 * The subject implementation to move the files to their target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MoveFilesSubject extends AbstractSubject
{

    /**
     * Imports the content of the file with the passed filename.
     *
     * @param string $serial   The unique process serial
     * @param string $filename The filename to process
     *
     * @return void
     * @throws \Exception Is thrown, if the import can't be processed
     */
    public function import($serial, $filename)
    {

        // stop processing, if the filename doesn't match
        if (!$this->match($filename)) {
            return;
        }

        // initialize the global global data to import a bunch
        $this->setUp();

        // initialize serial and filename
        $this->setSerial($serial);
        $this->setFilename($filename);

        // query whether the new source directory has to be created or not
        if (!is_dir($newSourceDir = $this->getNewSourceDir())) {
            mkdir($newSourceDir);
        }

        // move the file to the new source directory
        rename($filename, sprintf('%s/%s', $newSourceDir, basename($filename)));

        // clean up the data after importing the bunch
        $this->tearDown();
    }
}
