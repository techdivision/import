<?php

/**
 * TechDivision\Import\Subjects\FileResolver\SimpleFileResolverInstance
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

namespace TechDivision\Import\Subjects\FileResolver;

/**
 * Wrapper for the simple file resolver implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SimpleFileResolverInstance extends AbstractFileResolver
{

    /**
     * Queries whether or not, the passed filename should be handled by the subject.
     *
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the file should be handled, else FALSE
     */
    public function shouldBeHandled($filename)
    {
        return parent::shouldBeHandled($filename);
    }
}
