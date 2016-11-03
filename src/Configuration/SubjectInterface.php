<?php

/**
 * TechDivision\Import\Configuration\SubjectInterface
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

namespace TechDivision\Import\Configuration;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
interface SubjectInterface
{

    /**
     * Return's the subject's class name.
     *
     * @return string The subject's class name
     */
    public function getClassName();

    /**
     * Return's the subject's identifier.
     *
     * @return string The subject's identifier
     */
    public function getIdentifier();

    /**
     * Return's the subject's source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat();

    /**
     * Return's the array with the subject's observers.
     *
     * @return array The subject's observers
     */
    public function getObservers();

    /**
     * Return's the array with the subject's callbacks.
     *
     * @return array The subject's callbacks
     */
    public function getCallbacks();
}
