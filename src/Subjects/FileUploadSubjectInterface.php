<?php

/**
 * TechDivision\Import\Subjects\FileUploadSubjectInterface
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
 * The interface for all subject implementations that supports file upload.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileUploadSubjectInterface extends FilesystemSubjectInterface
{

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
     * Return's the name of the created image.
     *
     * @return string The name of the created image
     */
    public function getParentImage();

    /**
     * Get new file name if the same is already exists.
     *
     * @param string $targetFilename The name of the exisising files
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
