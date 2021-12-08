<?php

/**
 * TechDivision\Import\Observers\EntitySubjectImpl
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Observers;

use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Subjects\EavSubjectInterface;
use TechDivision\Import\Subjects\EntitySubjectInterface;

/**
 * Test class for a entity subject implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class EntitySubjectImpl implements SubjectInterface, EavSubjectInterface, EntitySubjectInterface
{
}
