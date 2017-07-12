<?php

/**
 * TechDivision\Import\Observers\AbstractObserverImpl
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

namespace TechDivision\Import\Observers;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Test class for the abstract observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractObserverImpl extends AbstractObserver
{

    /**
     * Set's the obeserver's subject instance to initialize the observer with.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The observer's subject
     */
    public function __construct(SubjectInterface $subject)
    {
        $this->setSubject($subject);
    }

    /**
     * Initialize's and return's a new entity with the status 'create'.
     *
     * @param array $attr The attributes to merge into the new entity
     *
     * @return array The initialized entity
     */
    public function initializeEntity(array $attr = array())
    {
        return parent::initializeEntity($attr);
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * status to 'update'.
     *
     * @param array $entity The entity to merge the attributes into
     * @param array $attr   The attributes to be merged
     *
     * @return array The merged entity
     */
    public function mergeEntity(array $entity, array $attr)
    {
        return parent::mergeEntity($entity, $attr);
    }
}
