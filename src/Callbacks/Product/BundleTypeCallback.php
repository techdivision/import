<?php

/**
 * TechDivision\Import\Callbacks\Product\BundleTypeCallback
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Callbacks\Product;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class BundleTypeCallback extends AbstractProductImportCallback
{

    /**
     * Array with the string => boolean mapping.
     *
     * @var array
     */
    protected $types = array(
        'fixed'  => 1,
        'dynamic' => 0
    );

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Callbacks\Product\ImportCallbackInterface::handle()
     */
    public function handle($value)
    {
        return (boolean) $this->types[strtolower($value)];
    }
}
