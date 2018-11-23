<?php

/**
 * TechDivision\Import\Serializers\AbstractSerializer
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

namespace TechDivision\Import\Serializers;

use TechDivision\Import\Configuration\CsvConfigurationInterface;

/**
 * Abstract serializer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractCsvSerializer implements SerializerInterface, ConfigurationAwareSerializerInterface
{

    /**
     * The configuration used to un-/serialize the additional attributes.
     *
     * @var \TechDivision\Import\Configuration\CsvConfigurationInterface
     */
    private $configuration;

    /**
     * Passes the configuration and initializes the serializer.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The CSV configuration
     *
     * @return void
     */
    public function init(CsvConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the configuration to un-/serialize the additional attributes.
     *
     * @return \TechDivision\Import\Configuration\CsvConfigurationInterface The configuration
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }
}
