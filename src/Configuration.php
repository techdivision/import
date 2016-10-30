<?php

/**
 * TechDivision\Import\Configuration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Annotation\SerializedName;

/**
 * A simple configuration implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Configuration implements ConfigurationInterface
{

    /**
     * The file prefix for import files.
     *
     * @var string
     * @Type("string")
     */
    protected $prefix = 'magento-import';

    /**
     * The Magento edition, EE or CE.
     *
     * @var string
     * @Type("string")
     * @SerializedName("magento-edition")
     */
    protected $magentoEdition = 'CE';

    /**
     * The Magento version, e. g. 2.1.0.
     *
     * @var string
     * @Type("string")
     * @SerializedName("magento-version")
     */
    protected $magentoVersion = '2.1.2';

    /**
     * The source directory that has to be watched for new files.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-dir")
     */
    protected $sourceDir;

    /**
     * The database configuration.
     *
     * @var TechDivision\Import\Configuration\Database
     * @Type("TechDivision\Import\Configuration\Database")
     */
    protected $database;

    /**
     * ArrayCollection with the information of the configured subjects.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Configuration\Subject>")
     */
    protected $subjects;

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * @param string $filename The name of the file with the configuration data
     *
     * @return \TechDivision\Import\Configuration The configuration instance
     */
    public static function factory($filename)
    {

        // load the JSON data
        if (!$jsonData = file_get_contents($filename)) {
            throw new \Exception('Can\'t load configuration file $filename');
        }

        // initialize the JMS serializer and load the configuration
        $serializer = SerializerBuilder::create()->build();
        return $serializer->deserialize($jsonData, 'TechDivision\Import\Configuration', 'json');
    }

    /**
     * Return's the database configuration.
     *
     * @return TechDivision\Import\Configuration\Database The database configuration
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Return's the ArrayCollection with the subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the subjects
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * Return's the Magento edition, EE or CE.
     *
     * @return string The Magento edition
     */
    public function getMagentoEdition()
    {
        return $this->magentoEdition;
    }

    /**
     * Return's the Magento version, e. g. 2.1.0.
     *
     * @return string The Magento version
     */
    public function getMagentoVersion()
    {
        return $this->magentoVersion;
    }
}
