<?php

/**
 * TechDivision\Import\Configuration\OperationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * Interface for the operation configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface OperationInterface
{

    /**
     * Query's whether or not the passed operation equals this instance.
     *
     * @param \TechDivision\Import\Cli\Configuration\Operation $operation The operation to query
     *
     * @return boolean TRUE if the operations are equal, else FALSE
     */
    public function equals(OperationInterface $operation);

    /**
     * Return's the operation's name.
     *
     * @return string The operation's class name
     */
    public function getName();

    /**
     * Return's the ArrayCollection with the operation's subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operation's subjects
     */
    public function getSubjects();

    /**
     * String representation of the operation (the name).
     *
     * @return string The operation name
     */
    public function __toString();
}
