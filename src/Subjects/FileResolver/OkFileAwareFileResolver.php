<?php

/**
 * TechDivision\Import\Subjects\FileResolver\OkFileAwareFileResolver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileResolver;

use TechDivision\Import\Exceptions\MissingOkFileException;

/**
 * Plugin that processes the subjects.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileAwareFileResolver extends AbstractFileResolver
{

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     * @throws \Exception Is thrown, when the source directory is NOT available
     * @throws \TechDivision\Import\Exceptions\MissingOkFileException Is thrown, if files to be processed are available but the mandatory OK file is missing
     */
    public function loadFiles(string $serial) : array
    {

        // initialize the array with the files that has to be handled
        $filesToHandle = parent::loadFiles($serial);

        // load the size of the files before the filters have been applied
        $sizeBeforeFiltersHaveBeenApplied = $this->getFilesystemLoader()->getSizeBeforeFiltersHaveBeenApplied();

        // stop processing, if files ARE available, an OK file IS mandatory, but
        // NO file will be processed (because of a missing/not matching OK file)
        if ($this->getSubjectConfiguration()->isOkFileNeeded() && $sizeBeforeFiltersHaveBeenApplied > 0 && sizeof($filesToHandle) === 0) {
            throw new MissingOkFileException(
                sprintf(
                    'Stop processing, because can\'t find the mandatory OK file to process at least one of %d files',
                    $sizeBeforeFiltersHaveBeenApplied
                )
            );
        }

        // return the array with the files that has to be handled
        return $filesToHandle;
    }
}
