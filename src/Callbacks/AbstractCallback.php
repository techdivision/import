<?php

/**
 * TechDivision\Import\Callbacks\AbstractCallback
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

namespace TechDivision\Import\Callbacks;

/**
 * An abstract callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractCallback implements CallbackInterface
{

    /**
     * Initializes the observer with the passed subject instance.
     *
     * @param object|null $subject The observer's subject instance
     */
    public function __construct($subject = null)
    {
        if ($subject != null) {
            $this->setSubject($subject);
        }
    }

    /**
     * Set's the obeserver's subject instance to initialize the observer with.
     *
     * @param object $subject The observer's subject
     *
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Return's the observer's subject instance.
     *
     * @return object The observer's subject instance
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Return's the system logger.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {
        return $this->getSubject()->getSystemLogger();
    }
}
