<?php

/**
 * TechDivision\Import\Serializers\SerializerAwareInterface
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

/**
 * The interface for all instances that provides serializer functionality.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2021 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import
 * @link       http://www.techdivision.com
 * @deprecated Since 16.8.3
 * @see        \TechDivision\Import\Serializer\SerializerAwareInterface
 */
interface SerializerAwareInterface
{

    /**
     * Sets the serializer instance.
     *
     * @param \TechDivision\Import\Serializers\SerializerInterface $serializer The serializer instance
     *
     * @return void
     */
    public function setSerializer(SerializerInterface $serializer);

    /**
     * Returns the serializer instance.
     *
     * @return \TechDivision\Import\Serializers\SerializerInterface The serializer instance
     */
    public function getSerializer();
}
