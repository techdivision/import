<?php

/**
 * TechDivision\Import\Subjects\ValidatorSubject
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\RegistryKeys;

/**
 * Generic validator subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ValidatorSubject extends AbstractSubject
{

    /**
     * The validation errors.
     *
     * @var array
     */
    protected $validations = array();

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // invoke the parent method
        parent::tearDown($serial);

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the source directory for the next subject
        $registryProcessor->mergeAttributesRecursive(RegistryKeys::STATUS, array(RegistryKeys::VALIDATIONS => $this->getValidations()));

        // log a debug message with the new source directory
        $this->getSystemLogger()->debug(
            sprintf('Subject %s successfully updated validation data for import %s', get_class($this), $serial)
        );
    }

    /**
     * Return's the array with the validation errors.
     *
     * @return array The validation errors
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * Merge the passed validation errors into the status.
     *
     * @param array $validations The validation errors to merge
     *
     * @return void
     */
    public function mergeValidations(array $validations)
    {
        $this->validations = array_replace_recursive($this->validations, $validations);
    }
}
