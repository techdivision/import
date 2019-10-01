<?php

/**
 * TechDivision\Import\Configuration\ExecutionContext
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
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */
namespace TechDivision\Import\Configuration;

/**
 * Interface for implementations that contains data about the actual execution context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */
interface ExecutionContextConfigurationInterface
{

    /**
     * Return's the Magento Edition to use in the actual execution context.
     *
     * @return string The Magento Edition
     */
    public function getMagentoEditiion();

    /**
     * Return's the Entity Type Code to use in the actual execution context.
     *
     * @return string The Entity Type
     */
    public function getEntityTypeCode();
}

