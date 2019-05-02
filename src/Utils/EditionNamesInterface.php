<?php

/**
 * TechDivision\Import\Utils\EditionNamesInterface
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

namespace TechDivision\Import\Utils;

/**
 * Interface for an utility class containing the supported Magento edition names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface EditionNamesInterface
{

    /**
     * Name for the member 'CE'.
     *
     * @var string
     */
    const CE = 'CE';

    /**
     * Name for the member 'EE'.
     *
     * @var string
     */
    const EE = 'EE';

    /**
     * Query whether or not the passed edition name is valid.
     *
     * @param string $editionName The edition name to query for
     *
     * @return boolean TRUE if the edition name is valid, else FALSE
     */
    public function isEditionName($editionName);
}
