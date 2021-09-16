<?php

/**
 * TechDivision\Import\Cli\Subjects\MoveFilesSubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\RegistryKeys;

/**
 * The subject implementation to move the files to their target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MoveFilesSubject extends AbstractSubject
{

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update target and source directory for the next subject
        $registryProcessor->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            array(
                RegistryKeys::TARGET_DIRECTORY => $newSourceDir = $this->getNewSourceDir($serial),
                RegistryKeys::SOURCE_DIRECTORY => $newSourceDir
            )
        );

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated source directory to %s', get_class($this), $newSourceDir)
        );

        // invoke the parent method
        parent::tearDown($serial);
    }

    /**
     * Return's the header mappings for the actual entity.
     *
     * @return array The header mappings
     */
    public function getHeaderMappings()
    {
        return array();
    }

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings()
    {
        return array();
    }

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @param string $serial The serial of the actual import
     *
     * @return string The new source directory
     */
    public function getNewSourceDir($serial)
    {
        return sprintf('%s/%s', $this->getConfiguration()->getSourceDir(), $serial);
    }

    /**
     * Imports the content of the file with the passed filename.
     *
     * @param string $serial   The serial of the actual import
     * @param string $filename The filename to process
     *
     * @return void
     * @throws \Exception Is thrown, if the import can't be processed
     */
    public function import($serial, $filename)
    {

        // initialize the serial/filename
        $this->setSerial($serial);
        $this->setFilename($filename);

        // query whether the new source directory has to be created or not
        if (!$this->isDir($newSourceDir = $this->getNewSourceDir($serial))) {
            $this->mkdir($newSourceDir);
        }

        // move the file to the new source directory
        $this->rename($filename, sprintf('%s/%s', $newSourceDir, basename($filename)));

        // update the status
        $this->mergeStatus(
            array(
                RegistryKeys::STATUS => array(
                    RegistryKeys::FILES => array(
                        $filename => array(
                            $this->getUniqueId() => array(
                                RegistryKeys::STATUS         => 1,
                                RegistryKeys::SKIPPED_ROWS   => $this->getSkippedRows(),
                                RegistryKeys::PROCESSED_ROWS => $this->getLineNumber() - 1
                            )
                        )
                    )
                )
            )
        );
    }
}
