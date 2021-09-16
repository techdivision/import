<?php

/**
 * TechDivision\Import\Subjects\FileUploadSubjectInterface
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

/**
 * The interface for all subject implementations that supports file upload.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileUploadSubjectInterface extends FilesystemSubjectInterface
{

    /**
     * Adds the mapping from the filename => new filename.
     *
     * @param string $filename    The filename
     * @param string $newFilename The new filename
     *
     * @return string The mapped filename
     */
    public function addImageMapping($filename, $newFilename);

    /**
     * Returns the mapped filename (which is the new filename).
     *
     * @param string $filename The filename to map
     *
     * @return string The mapped filename
     */
    public function getImageMapping($filename);

    /**
     * Returns TRUE, if the passed filename has already been mapped.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the filename has already been mapped, else FALSE
     */
    public function imageHasBeenMapped($filename);

    /**
     * Returns TRUE, if the passed filename has NOT been mapped yet.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the filename has NOT been mapped yet, else FALSE
     */
    public function imageHasNotBeenMapped($filename);

    /**
     * Returns the original filename for passed one (which is the new filename).
     *
     * @param string $newFilename The new filename to return the original one for
     *
     * @return string The original filename
     */
    public function getInversedImageMapping($newFilename);

    /**
     * Return's the flag to copy images or not.
     *
     * @return boolean The flag
     */
    public function hasCopyImages();

    /**
     * Return's the directory with the Magento media files => target directory for images.
     *
     * @return string The directory with the Magento media files => target directory for images
     */
    public function getMediaDir();

    /**
     * Return's the directory with the images that have to be imported.
     *
     * @return string The directory with the images that have to be imported
     */
    public function getImagesFileDir();

    /**
     * Get new file name, if a filename with the same name already exists.
     *
     * @param string $targetFilename The name of target file
     *
     * @return string The new filename
     */
    public function getNewFileName($targetFilename);

    /**
     * Upload's the file with the passed name to the Magento
     * media directory. If the file already exists, the will
     * be given a new name that will be returned.
     *
     * @param string $filename The name of the file to be uploaded
     *
     * @return string The name of the uploaded file
     * @throws \Exception Is thrown, if the file with the passed name is not available
     */
    public function uploadFile($filename);
}
