<?php

/**
 * TechDivision\Import\Observers\Product\PreImport\AdditionalAttributeObserver
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

namespace TechDivision\Import\Observers\Product\PreImport;

use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Observers\Product\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class AdditionalAttributeObserver extends AbstractProductImportObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not the row has additional attributes
        if ($additionalAttributes = $row[$headers[ColumnKeys::ADDITIONAL_ATTRIBUTES]]) {
            // query if the additional attributes have a value, at least
            if (strstr($additionalAttributes, '=') === false) {
                return;
            }

            // explode the additional attributes
            $additionalAttributes = explode(',', $additionalAttributes);

            // iterate over the attributes and append them to the row
            foreach ($additionalAttributes as $additionalAttribute) {
                // explode attribute code/option value from the attribute
                list ($attributeCode, $optionValue) = explode('=', $additionalAttribute);

                // try to load the appropriate key for the value
                if (isset($headers[$attributeCode])) {
                    $newKey = $headers[$attributeCode];
                } else {
                    $headers[$attributeCode] = $newKey = sizeof($headers);
                }

                // append/replace the attribute value
                $row[$newKey] = $optionValue;
            }
        }

        // update the header information
        $this->setHeaders($headers);

        // return the prepared row
        return $row;
    }
}
