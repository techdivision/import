<?php

/**
 * TechDivision\Import\Repositories\Finders\YieldedFinder
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

namespace TechDivision\Import\Repositories\Finders;

/**
 * Yielded finder implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class YieldedFinder extends SimpleFinder
{

    /**
     * Executes the finder with the passed parameters.
     *
     * @param array The finder params
     *
     * @return array The finder result
     */
    public function find(array $params = array())
    {

        // execute the prepared statement
        $this->preparedStatement->execute($params);

        // fetch the values and return them
        while ($record = $this->preparedStatement->fetch(\PDO::FETCH_ASSOC)) {
            // return the record
            yield $record;
        }
    }
}
