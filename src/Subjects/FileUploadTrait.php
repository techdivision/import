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
     * The trait that provides basic filesystem handling functionality.
     *
     * @var TechDivision\Import\Subjects\FilesystemTrait
     */
    use FilesystemTrait;

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
     * The name of the craeted image.
     *
     * @var integer
     */
    protected $parentImage;

    /**
     * The flag whether to copy the images or not.
     *
     * @var boolean
     */
    protected $copyImages;

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
     * Set's the name of the created image.
     *
     * @param string $parentImage The name of the created image
     *
     * @return void
     */
    public function setParentImage($parentImage)
    {
        $this->parentImage = $parentImage;
    }

    /**
     * Return's the name of the created image.
     *
     * @return string The name of the created image
     */
    public function getParentImage()
    {
        return $this->parentImage;
    }

    /**
     * Get new file name if the same is already exists.
     *
     * @param string $targetFilename The name of the exisising files
     *
     * @return string The new filename
     */
    public function getNewFileName($targetFilename)
    {

        // load the file information
        $fileInfo = pathinfo($targetFilename);

        // query whether or not, the file exists
        if ($this->getFilesystem()->has($targetFilename)) {
            // initialize the incex and the basename
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];

            // prepare the new filename by raising the index
            while ($this->getFilesystem()->has($fileInfo['dirname'] . '/' . $baseName)) {
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

        // prepare source/target filename
        $sourceFilename = sprintf('%s/%s', $this->getImagesFileDir(), $trimmedFilename);
        $targetFilename = sprintf('%s/%s', $this->getMediaDir(), $trimmedFilename);

        // query whether or not the image file to be imported is available
        if (!$this->getFilesystem()->has($sourceFilename)) {
            throw new \Exception(sprintf('Media file %s not available', $sourceFilename));
        }

        // prepare the target filename, if necessary
        $newTargetFilename = $this->getNewFileName($targetFilename);
        $targetFilename = str_replace(basename($targetFilename), $newTargetFilename, $targetFilename);

        // copy the image to the target directory
        $this->getFilesystem()->copy($sourceFilename, $targetFilename);

        // return the new target filename
        return str_replace($this->getMediaDir(), '', $targetFilename);
    }
}
