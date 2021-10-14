<?php

/**
 * TechDivision\Import\Subjects\PhpFilesystemAdapterFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * A filesystem adapter factory implementation for filesystem implementation that uses plain PHP functions.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PhpFilesystemAdapterFactory implements FilesystemAdapterFactoryInterface
{

    /**
     * Factory method to create new filesystem adapter instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function createFilesystemAdapter(SubjectConfigurationInterface $subjectConfiguration)
    {
        return new PhpFilesystemAdapter();
    }
}
