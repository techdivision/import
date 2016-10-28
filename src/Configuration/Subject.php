<?php

/**
 * TechDivision\Import\Configuration\Subject
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

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Subject implements SubjectInterface
{

    /**
     * The subject's class name.
     *
     * @var string
     * @Type("string")
     * @SerializedName("class-name")
     */
    protected $className;

    /**
     * The subject's identifier.
     *
     * @var string
     * @Type("string")
     */
    protected $identifier;

    /**
     * Return's the source date format to use in the subject.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-date-format")
     */
    protected $sourceDateFormat = 'n/d/y, g:i A';

    /**
     * The array with the subject's callbacks.
     *
     * @var array
     * @Type("array")
     */
    protected $callbacks;

    /**
     * Return's the subject's class name.
     *
     * @return string The subject's class name
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Return's the subject's identifier.
     *
     * @return string The subject's identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Return's the subject's source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->sourceDateFormat;
    }

    /**
     * Return's the array with the subject's callbacks.
     *
     * @return array The subject's callbacks
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }
}
