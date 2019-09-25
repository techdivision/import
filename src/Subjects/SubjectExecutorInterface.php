<?php

/**
 * TechDivision\Import\Subjects\SubjectExecutorInterface
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

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * The interface for all subject executor implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface SubjectExecutorInterface
{

    /**
     * Executes the passed subject.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject  The message with the subject information
     * @param array                                                            $matches  The bunch matches
     * @param string                                                           $serial   The UUID of the actual import
     * @param string                                                           $pathname The path to the file to import
     *
     * @return void
     */
    public function execute(SubjectConfigurationInterface $subject, array $matches, $serial, $pathname);
}
