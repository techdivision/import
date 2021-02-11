<?php

/**
 * TechDivision\Import\Observers\GenericColumnCollectorObserver
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Observers;

use Ramsey\Uuid\Uuid;

/**
 * Observer that loads the data of a set of configurable columns
 * into the registry for further processing, e. g. validation.
 *
 * As index the column name and a UUID will be used, whereas it
 * is not clear to which entity the collected data belongs. This
 * observer can therefore be used when only the collected data
 * is of interest, for example you want to have a list of SKUs
 * that are part of the import file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericColumnCollectorObserver extends AbstractColumnCollectorObserver
{

    /**
     * Return's the primary key value that will be used as second incdex.
     *
     * @return string The primary key to be used
     */
    protected function getPrimaryKey() : string
    {
        return Uuid::uuid4()->toString();
    }
}
