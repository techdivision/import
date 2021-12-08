<?php

/**
 * TechDivision\Import\Subjects\FileUploadTraitImpl
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Subjects;

/**
 * Test class for the file upload trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FileUploadTraitImpl
{

    /**
     * The filesystem trait.
     *
     * @var \TechDivision\Import\Subjects\FilesystemTrait
     */
    use FilesystemTrait;

    /**
     * The file upload trait we want to test.
     *
     * @var \TechDivision\Import\Subjects\FileUploadTrait
     */
    use FileUploadTrait;
}
