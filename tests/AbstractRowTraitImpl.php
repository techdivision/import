<?php

/**
 * TechDivision\Import\AbstractRowTraitImpl
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

namespace TechDivision\Import;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Wrapper for a subject that uses the row trait implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
