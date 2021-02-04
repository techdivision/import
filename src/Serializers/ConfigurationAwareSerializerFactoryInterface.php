<?php

/**
 * TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Serializers;

use TechDivision\Import\Configuration\CsvConfigurationInterface;

/**
 * The factory implementation for configuration aware serializer instances.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Serializer\SerializerFactoryInterface
 */
interface ConfigurationAwareSerializerFactoryInterface
{

    /**
     * Creates and returns the serializer instance.
     *
     * @param \TechDivision\Import\Configuration\CsvConfigurationInterface $configuration The serializer configuration
     *
     * @return \TechDivision\Import\Serializers\ConfigurationAwareSerializerInterface The serializer instance
     */
    public function createSerializer(CsvConfigurationInterface $configuration);
}
