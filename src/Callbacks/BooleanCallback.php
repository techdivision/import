<?php

/**
 * TechDivision\Import\Callbacks\BooleanCallback
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
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

/**
 * A callback implementation that converts the passed boolean value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class BooleanCallback extends AbstractCallback
{

    /**
     * Array with the string => boolean mapping.
     *
     * @var array
     */
    protected $booleanValues = array(
        'true'  => 1,
        'yes'   => 1,
        '1'     => 1,
        'false' => 0,
        'no'    => 0,
        '0'     => 0
    );

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param mixed $value The value to handle
     *
     * @return mixed The modified value
     * @see \TechDivision\Import\Product\Callbacks\ProductImportCallbackInterface::handle()
     */
    public function handle($value)
    {
        return (boolean) $this->booleanValues[strtolower($value)];
    }
}
