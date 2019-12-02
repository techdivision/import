<?php

/**
 * TechDivision\Import\Callbacks\AbstractValidatorCallback
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

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Abstract callback implementation the validate the value for an specific attribute.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractValidatorCallback implements CallbackInterface, CallbackFactoryInterface
{

    /**
     * The loader instance for the custom validations.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The subject instance the serializer is bound to.
     *
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    protected $subject;

    /**
     * The array that contains the allowed values found in the configuration.
     *
     * @var array
     */
    protected $validations = array();

    /**
     * Initializes the callback with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader The loader for the validations
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Will be invoked by the callback visitor when a factory has been defined to create the callback instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Callbacks\CallbackInterface The callback instance
     */
    public function createCallback(SubjectInterface $subject)
    {

        // set the subject
        $this->setSubject($subject);

        // set the validations
        $this->setValidations($this->getLoader()->load($subject->getConfiguration()));

        // return the initialized instance
        return $this;
    }

    /**
     * Set's the subject instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return void
     */
    protected function setSubject(SubjectInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Return's the subject instance.
     *
     * @return \TechDivision\Import\Subjects\SubjectInterface The subject instance
     */
    protected function getSubject()
    {
        return $this->subject;
    }

    /**
     * Return's the loader instance for the custom validations.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set's the validations.
     *
     * @param array $validations The available validations
     *
     * @return void
     */
    protected function setValidations(array $validations)
    {
        $this->validations = $validations;
    }

    /**
     * Return's the validations for the attribute with the passed code.
     *
     * @param string|null $attributeCode The code of the attribute to return the validations for
     *
     * @return array The allowed values for the attribute with the passed code
     */
    protected function getValidations($attributeCode = null)
    {
        return $this->validations;
    }
}
