<?php

/**
 * TechDivision\Import\Observers\FileUploadObserver
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Abstract observer that uploads the file specified in a CSV file's column
 * 'image_path' to a configurable directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractFileUploadObserver extends AbstractObserver
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, the image changed
        if ($this->isParentImage($image = $this->getValue($this->getSourceColumn()))) {
            return;
        }

        // initialize the image path
        $imagePath = $this->getValue($this->getSourceColumn());

        // load the subjet
        $subject = $this->getSubject();

        // query whether or not we've to upload the image files
        if ($subject->hasCopyImages()) {
            // upload the file and set the new image path
            $imagePath = $subject->uploadFile($image);

            // log a message that the image has been copied
            $subject->getSystemLogger()->debug(
                sprintf('Successfully copied image %s => %s', $image, $imagePath)
            );
        }

        // write the real image path to the target column
        $this->setValue($this->getTargetColumn(), $imagePath);
    }

    /**
     * Return's TRUE if the passed image is the parent one.
     *
     * @param string $image The imageD to check
     *
     * @return boolean TRUE if the passed image is the parent one
     */
    protected function isParentImage($image)
    {
        return $this->getSubject()->getParentImage() === $image;
    }

    /**
     * Return's the name of the source column with the image path.
     *
     * @return string The image path
     */
    abstract protected function getSourceColumn();

    /**
     * Return's the target column with the path of the copied image.
     *
     * @return string The path to the copied image
     */
    abstract protected function getTargetColumn();
}
