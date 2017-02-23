<?php

/**
 * TechDivision\Import\Observers\UrlObserver
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

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Utils\Filter\ConvertLiteralUrl;

/**
 * Observer that extracts the URL key/path from the path and adds a two new columns
 * with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlObserver extends AbstractObserver
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param array $row The row to handle
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(array $row)
    {

        // initialize the row
        $this->setRow($row);

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the URL key and array for the categories
        $urlKey = null;
        $urlPath = array();
        $categories = array();

        // explode the category names from the path
        if ($path = $this->getValue(ColumnKeys::PATH)) {
            $categories = $this->explode($path, '/');
        }

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } elseif (sizeof($categories) > 0) {
            $this->setValue(ColumnKeys::URL_KEY, $urlKey = $this->convertNameToUrlKey(end($categories)));
        } else {
            $this->getSystemLogger()->debug(
                sprintf(
                    'Can\'t find an URL key or a path in CSV file %s on line %d',
                    $this->getFilename(),
                    $this->getLineNumber()
                )
            );
            return;
        }

        // add the column for the URL path, if not yet available
        if (!$this->hasHeader(ColumnKeys::URL_PATH)) {
            $this->addHeader(ColumnKeys::URL_PATH);
        }

        // prepare the URL path
        if (sizeof($categories) > 0) {
            $urlPath = array_slice($categories, 1, sizeof($categories) - 2);
        }

        // convert the elements of the URL path
        array_walk($urlPath, function (&$value) {
            $value = $this->convertNameToUrlKey($value);
        });

        // add the URL key to the URL path
        array_push($urlPath, $urlKey);

        // append the column with the URL path
        $this->setValue(ColumnKeys::URL_PATH, implode('/', $urlPath));
    }

    /**
     * Initialize's and return's the URL key filter.
     *
     * @return \TechDivision\Import\Product\Utils\ConvertLiteralUrl The URL key filter
     */
    protected function getUrlKeyFilter()
    {
        return new ConvertLiteralUrl();
    }

    /**
     * Convert's the passed string into a valid URL key.
     *
     * @param string $string The string to be converted, e. g. the product name
     *
     * @return string The converted string as valid URL key
     */
    protected function convertNameToUrlKey($string)
    {
        return $this->getUrlKeyFilter()->filter($string);
    }
}
