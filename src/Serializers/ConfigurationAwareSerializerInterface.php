<?php

/**
 * TechDivision\Import\Serializers\ConfigurationAwareSerializerInterface
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
 * Interface for configuration aware serializer implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Serializer\ConfigurationAwareSerializerInterface
 */
interface ConfigurationAwareSerializerInterface extends SerializerInterface
{

    /**
     * Passes the configuration and initializes the serializer.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The CSV configuration
     *
     * @return void
     */
    public function init(CsvConfigurationInterface $configuration);
}
