<?php

/**
 * TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\FileResolver;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Interface for all file resolver factory instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileResolverFactoryInterface
{

    /**
     * Creates and returns the file resolver instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the file resolver for
     *
     * @return \TechDivision\Import\Subjects\FileResolver\FileResolverInterface The file resolver instance
     */
    public function createFileResolver(SubjectConfigurationInterface $subject) : FileResolverInterface;
}
