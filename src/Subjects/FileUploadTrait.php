<?php

/**
 * TechDivision\Import\Subjects\FileUploadTrait
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
 * The trait implementation for the file upload functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait FileUploadTrait
{

    /**
     * The directory with the Magento media files => target directory for images (relative to the root directory).
     *
     * @var string
     */
    protected $mediaDir;

    /**
     * The directory with the images that have to be imported (relative to the root directory).
     *
     * @var string
     */
    protected $imagesFileDir;

    /**
     * Contains the mappings for the image names that has been uploaded (old => new image name).
     *
     * @var array
     */
    protected $imageMappings = array();

    /**
     * The flag whether to copy the images or not.
     *
     * @var boolean
     */
    protected $copyImages = false;

    /**
     * Whether or not to override images with the same name.
     * TODO: Refactor to make protected
     *
     * @var boolean
     */
    private $overrideImages = false;

    /**
     * Sets whether or not to override images with the same name.
     * TODO: Refactor to make public
     *
     * @param boolean $overrideImages Whether or not to override images
     *
     * @return void
     */
    private function setOverrideImages($overrideImages)
    {
        $this->overrideImages = $overrideImages;
    }

    /**
     * Returns whether or not we should override images with the same name.
     * TODO: Refactor to make public or protected
     *
     * @return bool
     */
    private function shouldOverride()
    {
        return $this->overrideImages;
    }

    /**
     * Set's the flag to copy the images or not.
     *
     * @param boolean $copyImages The flag
     *
     * @return void
     */
    public function setCopyImages($copyImages)
    {
        $this->copyImages = $copyImages;
    }

    /**
     * Return's the flag to copy images or not.
     *
     * @return boolean The flag
     */
    public function hasCopyImages()
    {
        return $this->copyImages;
    }

    /**
     * Set's directory with the Magento media files => target directory for images.
     *
     * @param string $mediaDir The directory with the Magento media files => target directory for images
     *
     * @return void
     */
    public function setMediaDir($mediaDir)
    {
        $this->mediaDir = $mediaDir;
    }

    /**
     * Return's the directory with the Magento media files => target directory for images.
     *
     * @return string The directory with the Magento media files => target directory for images
     */
    public function getMediaDir()
    {
        return $this->mediaDir;
    }

    /**
     * Set's directory with the images that have to be imported.
     *
     * @param string $imagesFileDir The directory with the images that have to be imported
     *
     * @return void
     */
    public function setImagesFileDir($imagesFileDir)
    {
        $this->imagesFileDir = $imagesFileDir;
    }

    /**
     * Return's the directory with the images that have to be imported.
     *
     * @return string The directory with the images that have to be imported
     */
    public function getImagesFileDir()
    {
        return $this->imagesFileDir;
    }

    /**
     * Adds the mapping from the filename => new filename.
     *
     * @param string $filename    The filename
     * @param string $newFilename The new filename
     *
     * @return void
     */
    public function addImageMapping($filename, $newFilename)
    {
        $this->imageMappings[$filename] = $newFilename;
    }

    /**
     * Returns the mapped filename (which is the new filename).
     *
     * @param string $filename The filename to map
     *
     * @return string The mapped filename
     */
    public function getImageMapping($filename)
    {

        // query whether or not a mapping is available, if yes return the mapped name
        if (isset($this->imageMappings[$filename])) {
            return $this->imageMappings[$filename];
        }

        // return the passed filename otherwise
        return $filename;
    }

    /**
     * Returns TRUE, if the passed filename has already been mapped.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the filename has already been mapped, else FALSE
     */
    public function imageHasBeenMapped($filename)
    {
        return isset($this->imageMappings[$filename]);
    }

    /**
     * Returns TRUE, if the passed filename has NOT been mapped yet.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the filename has NOT been mapped yet, else FALSE
     */
    public function imageHasNotBeenMapped($filename)
    {
        return !isset($this->imageMappings[$filename]);
    }

    /**
     * Returns the original filename for passed one (which is the new filename).
     *
     * @param string $newFilename The new filename to return the original one for
     *
     * @return string The original filename
     */
    public function getInversedImageMapping($newFilename)
    {

        // try to load the original filename
        if ($filename = array_search($newFilename, $this->imageMappings)) {
            return $filename;
        }

        // return the new one otherwise
        return $newFilename;
    }

    /**
     * Get new file name, if a filename with the same name already exists.
     *
     * @param string $targetFilename The name of target file
     *
     * @return string The new filename
     */
    public function getNewFileName($targetFilename)
    {

        // load the file information
        $fileInfo = pathinfo($targetFilename);

        // query whether or not the file exists and if we should override it
        if ($this->getFilesystemAdapter()->isFile($targetFilename) && $this->shouldOverride() === false) {
            // initialize the index and the basename
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];

            // prepare the new filename by raising the index
            while ($this->getFilesystemAdapter()->isFile($fileInfo['dirname'] . '/' . $baseName)) {
                $baseName = $fileInfo['filename'] . '_' . $index . '.' . $fileInfo['extension'];
                $index++;
            }

            // set the new filename
            $targetFilename = $baseName;
        } else {
            // if not, simply return the filename
            return $fileInfo['basename'];
        }

        // return the new filename
        return $targetFilename;
    }

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
    public function uploadFile($filename)
    {

        // trim the leading /, if available
        $trimmedFilename = ltrim($filename, '/');
        $mediaDir = ltrim($this->getMediaDir(), '/');
        $imagesFileDir = ltrim($this->getImagesFileDir(), '/');

        // prepare source/target filename
        $sourceFilename = sprintf('%s/%s', $imagesFileDir, $trimmedFilename);
        $targetFilename = sprintf('%s/%s', $mediaDir, $trimmedFilename);

        // query whether or not the image file to be imported is available
        if (!$this->getFilesystemAdapter()->isFile($sourceFilename)) {
            throw new \Exception(sprintf('Media file "%s" is not available', $sourceFilename));
        }

        // query whether or not, the file has already been processed
        if ($this->imageHasNotBeenMapped($filename)) {
            // load the new filename, e. g. if a file with the same name already exists
            $newTargetFilename =  $this->getNewFileName($targetFilename);
            // replace the old filename with the new one
            $targetFilename = str_replace(basename($targetFilename), $newTargetFilename, $targetFilename);

            // make sure, the target directory exists
            if (!$this->getFilesystemAdapter()->isDir($targetDirectory = dirname($targetFilename))) {
                $this->getFilesystemAdapter()->mkdir($targetDirectory, 0755);
            }

            // copy the image to the target directory
            $this->getFilesystemAdapter()->copy($sourceFilename, $targetFilename);

            // add the mapping and return the mapped filename
            $this->addImageMapping($filename, str_replace($mediaDir, '', $targetFilename));
        }

        // simply return the mapped filename
        return $this->getImageMapping($filename);
    }

    /**
     * Delete the file with the passed name.
     *
     * @param string $filename The name of the file to be deleted
     *
     * @return boolean TRUE on success, else FALSE
     */
    public function deleteFile($filename)
    {

        // trim the leading /, if available
        $trimmedFilename = ltrim($filename, '/');
        $mediaDir = ltrim($this->getMediaDir(), '/');

        // prepare source/target filename
        $targetFilename = sprintf('%s/%s', $mediaDir, $trimmedFilename);

        // query whether or not the image file to be deleted is available
        if (!$this->getFilesystemAdapter()->isFile($targetFilename)) {
            throw new \Exception(sprintf('Media file "%s" is not available', $targetFilename));
        }

        // delte the image from the target directory
        $this->getFilesystemAdapter()->delete($targetFilename);
    }
}
