<?php

/**
 * TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface
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

/**
 * Interface for all attribute code and value aware observer implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface AttributeCodeAndValueAwareObserverInterface extends ObserverInterface
{

    /**
     * The attribute code that has to be processed.
     *
     * @return string The attribute code
     */
    public function getAttributeCode();

    /**
     * The attribute value that has to be processed.
     *
     * @return string The attribute value
     */
    public function getAttributeValue();
}
