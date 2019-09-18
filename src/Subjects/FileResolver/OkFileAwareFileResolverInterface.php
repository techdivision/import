<?php

/**
 * TechDivision\Import\Subjects\FileResolver\OkFileAwareFileResolverInterface
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
 * Interface for all OK file aware resolver implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface OkFileAwareFileResolverInterface extends FileResolverInterface
{

    /**
     * Query whether or not, the passed CSV filename is in the OK file. If the filename was found,
     * the OK file will be cleaned-up.
     *
     * @param string $filename The filename to be cleaned-up
     *
     * @return void
     * @throws \Exception Is thrown, if the passed filename is NOT in the OK file or the OK can not be cleaned-up
     */
    public function cleanUpOkFile($filename);
}
