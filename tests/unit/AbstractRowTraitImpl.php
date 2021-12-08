<?php

/**
 * TechDivision\Import\AbstractRowTraitImpl
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Wrapper for a subject that uses the row trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractRowTraitImpl implements SubjectInterface
{

    /**
     * The trait that provides header handling functionality.
     *
     * @var TechDivision\Import\HeaderTrait
     */
    use HeaderTrait;

    /**
     * The trait that provides row handling functionality.
     *
     * @var TechDivision\Import\RowTrait
     */
    use RowTrait;
}
