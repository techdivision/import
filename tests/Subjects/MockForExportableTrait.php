<?php

/**
 * TechDivision\Import\Subjects\MockForExportableTrait
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

use TechDivision\Import\ConfigurationInterface;

/**
 * Test class for the exportable trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MockForExportableTrait
{

    /**
     * The exportable trait we want to test.
     *
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    use ExportableTrait;

    /**
     * Set's the configuration instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     *
     * @return void
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set's the serial.
     *
     * @param string $serial The serial
     *
     * @return void
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
    }

    /**
     * Return's the serial.
     *
     * @return string The serial
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set's the filename.
     *
     * @param string $filename The filename
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Return's the filename.
     *
     * @return string The filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set's the line number.
     *
     * @param integer $lineNumber The line number
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
    }

    /**
     * Return's the line number.
     *
     * @return integer The line number
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * Set's the last entity ID.
     *
     * @param integer $lastEntityId The last entity ID
     */
    public function setLastEntityId($lastEntityId)
    {
        $this->lastEntityId = $lastEntityId;
    }

    /**
     * Return's the last entity ID.
     *
     * @return integer The last entity ID
     */
    public function getLastEntityId()
    {
        return $this->lastEntityId;
    }

    /**
     * Return's the next source directory, which will be the target directory
     * of this subject, in most cases.
     *
     * @param string $serial The serial of the actual import
     *
     * @return string The new source directory
     */
    protected function getNewSourceDir($serial)
    {
        return sprintf('%s/%s', $this->configuration->getTargetDir(), $serial);
    }
}
