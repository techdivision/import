<?php

/**
 * TechDivision\Import\Loaders\CustomValidationsLoader
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Configuration\ParamsConfigurationInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Loader for import artefacts.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CustomValidationsLoader implements LoaderInterface
{

    /**
     * Loads and returns data the custom validation data.
     *
     * @param \TechDivision\Import\Configuration\ParamsConfigurationInterface $configuration The configuration instance to load the validations from
     *
     * @return \ArrayAccess The array with the data
     */
    public function load(ParamsConfigurationInterface $configuration = null)
    {

        // return an empty array if the param has NOT been set
        $customValidations = array();

        if ($configuration === null) {
            return $customValidations;
        }

        if ($configuration instanceof PluginConfigurationInterface) {
            $customValidations = array_merge($customValidations, $this->load($configuration->getConfiguration()));
        }

        if ($configuration instanceof SubjectConfigurationInterface) {
            $customValidations = array_merge($customValidations, $this->load($configuration->getPluginConfiguration()));
        }

        if ($configuration->hasParam($name = ConfigurationKeys::CUSTOM_VALIDATIONS)) {
            $customValidations = array_merge($configuration->getParam($name));
        }

        return $customValidations;
    }
}
